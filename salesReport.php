<?php
include("connect.php");

// Set default values for filters
$startDate = isset($_GET['startDate']) ? $_GET['startDate'] : date('Y-m-01'); // First day of current month
$endDate = isset($_GET['endDate']) ? $_GET['endDate'] : date('Y-m-t'); // Last day of current month
$filterCompany = isset($_GET['company']) ? $_GET['company'] : '';
$groupBy = isset($_GET['groupBy']) ? $_GET['groupBy'] : 'none';

// Get unique companies for the dropdown filter
$companiesQuery = "SELECT DISTINCT company FROM order_form ORDER BY company";
$companiesResult = $conn->query($companiesQuery);
$companies = [];
while ($row = $companiesResult->fetch_assoc()) {
    $companies[] = $row['company'];
}

// Prepare the base query
$query = "SELECT * FROM order_form WHERE status != 'draft'";

// Add date filter
if ($startDate && $endDate) {
    $query .= " AND dateCreated BETWEEN '$startDate' AND '$endDate'";
}

// Add company filter
if ($filterCompany) {
    $query .= " AND company = '$filterCompany'";
}

// Add ordering
$query .= " ORDER BY dateCreated DESC";

// Execute the query
$result = $conn->query($query);

// Initialize summary data
$totalSales = 0;
$totalOrders = 0;
$companySales = [];
$monthlySales = [];
$itemSales = [];

// Process results
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $totalSales += $row['price'] * $row['quantity'];
        $totalOrders++;
        
        // Company summary
        if (!isset($companySales[$row['company']])) {
            $companySales[$row['company']] = [
                'orders' => 0,
                'revenue' => 0,
                'items' => 0
            ];
        }
        $companySales[$row['company']]['orders']++;
        $companySales[$row['company']]['revenue'] += $row['price'] * $row['quantity'];
        $companySales[$row['company']]['items'] += $row['quantity'];
        
        // Monthly summary
        $month = date('Y-m', strtotime($row['dateCreated']));
        if (!isset($monthlySales[$month])) {
            $monthlySales[$month] = 0;
        }
        $monthlySales[$month] += $row['price'] * $row['quantity'];
        
        // Item summary
        if (!isset($itemSales[$row['items']])) {
            $itemSales[$row['items']] = [
                'quantity' => 0,
                'revenue' => 0
            ];
        }
        $itemSales[$row['items']]['quantity'] += $row['quantity'];
        $itemSales[$row['items']]['revenue'] += $row['price'] * $row['quantity'];
    }
}

// Sort monthly sales by date
ksort($monthlySales);

// Sort company sales by revenue
uasort($companySales, function($a, $b) {
    return $b['revenue'] - $a['revenue'];
});

// Sort item sales by quantity
uasort($itemSales, function($a, $b) {
    return $b['quantity'] - $a['quantity'];
});

// Function to format currency
function formatCurrency($amount) {
    return '₱' . number_format($amount, 2);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sales Report</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="orderFormDashboard2.css">
    <style>
        .report-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }
        
        .filter-section {
            background-color: #f5f5f5;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
        }
        
        .filter-form {
            display: flex;
            gap: 15px;
            flex-wrap: wrap;
            align-items: flex-end;
        }
        
        .filter-group {
            flex: 1;
            min-width: 200px;
        }
        
        .filter-group label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }
        
        .filter-group input, .filter-group select {
            width: 100%;
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }
        
        .summary-cards {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }
        
        .summary-card {
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            padding: 20px;
            text-align: center;
        }
        
        .summary-card h3 {
            font-size: 1em;
            color: #666;
            margin: 0 0 10px 0;
        }
        
        .summary-card .value {
            font-size: 1.8em;
            font-weight: bold;
            color: #333;
        }
        
        .data-section {
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            margin-bottom: 30px;
            overflow: hidden;
        }
        
        .data-section h2 {
            padding: 15px 20px;
            margin: 0;
            background-color: #f5f5f5;
            border-bottom: 1px solid #ddd;
        }
        
        .data-table {
            width: 100%;
            border-collapse: collapse;
        }
        
        .data-table th, .data-table td {
            padding: 12px 15px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        
        .data-table th {
            background-color: #f9f9f9;
            font-weight: bold;
        }
        
        .data-table tr:last-child td {
            border-bottom: none;
        }
        
        .data-table tr:hover {
            background-color: #f5f5f5;
        }
        
        .chart-container {
            height: 300px;
            padding: 20px;
            overflow-x: auto;
        }
        
        .bar {
            fill: #4CAF50;
            transition: fill 0.3s;
        }
        
        .bar:hover {
            fill: #45a049;
        }
        
        .action-buttons {
            margin-bottom: 20px;
        }
        
        .btn-group {
            display: flex;
            gap: 10px;
            margin: 20px 0;
        }
        
        .tab {
            padding: 10px 15px;
            background-color: #f1f1f1;
            border-radius: 4px;
            cursor: pointer;
        }
        
        .tab.active {
            background-color: #4CAF50;
            color: white;
        }
        
        @media print {
            .filter-section, .action-buttons {
                display: none;
            }
            
            body {
                padding: 0;
                margin: 0;
            }
            
            .report-container {
                width: 100%;
                max-width: none;
                padding: 0;
            }
        }
    </style>
</head>
<body>
    <div class="report-container">
        <h1>Sales Report</h1>
        
        <div class="action-buttons">
            <a href="orderFormDashboard.php" class="btn btn-secondary">Back to Dashboard</a>
            <button onclick="window.print()" class="btn btn-primary"><i class="fas fa-print"></i> Print Report</button>
            <button onclick="exportToCSV()" class="btn btn-tertiary"><i class="fas fa-file-csv"></i> Export to CSV</button>
        </div>
        
        <div class="filter-section">
            <form class="filter-form" method="GET">
                <div class="filter-group">
                    <label for="startDate">Start Date</label>
                    <input type="date" id="startDate" name="startDate" value="<?php echo $startDate; ?>">
                </div>
                <div class="filter-group">
                    <label for="endDate">End Date</label>
                    <input type="date" id="endDate" name="endDate" value="<?php echo $endDate; ?>">
                </div>
                <div class="filter-group">
                    <label for="company">Company</label>
                    <select id="company" name="company">
                        <option value="">All Companies</option>
                        <?php foreach ($companies as $company): ?>
                            <option value="<?php echo htmlspecialchars($company); ?>" <?php echo $filterCompany == $company ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($company); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="filter-group">
                    <label for="groupBy">Group By</label>
                    <select id="groupBy" name="groupBy">
                        <option value="none" <?php echo $groupBy == 'none' ? 'selected' : ''; ?>>None</option>
                        <option value="company" <?php echo $groupBy == 'company' ? 'selected' : ''; ?>>Company</option>
                        <option value="month" <?php echo $groupBy == 'month' ? 'selected' : ''; ?>>Month</option>
                        <option value="item" <?php echo $groupBy == 'item' ? 'selected' : ''; ?>>Item</option>
                    </select>
                </div>
                <div class="filter-group" style="flex: 0;">
                    <button type="submit" class="btn btn-primary">Apply Filters</button>
                </div>
            </form>
        </div>
        
        <div class="summary-cards">
            <div class="summary-card">
                <h3>Total Revenue</h3>
                <div class="value"><?php echo formatCurrency($totalSales); ?></div>
            </div>
            <div class="summary-card">
                <h3>Total Orders</h3>
                <div class="value"><?php echo $totalOrders; ?></div>
            </div>
            <div class="summary-card">
                <h3>Average Order Value</h3>
                <div class="value">
                    <?php echo $totalOrders > 0 ? formatCurrency($totalSales / $totalOrders) : formatCurrency(0); ?>
                </div>
            </div>
            <div class="summary-card">
                <h3>Top Company</h3>
                <div class="value">
                    <?php 
                    $topCompany = !empty($companySales) ? array_key_first($companySales) : 'N/A';
                    echo htmlspecialchars($topCompany);
                    ?>
                </div>
            </div>
        </div>
        
        <div class="btn-group" id="dataTabs">
            <div class="tab active" data-target="companySection">By Company</div>
            <div class="tab" data-target="monthlySection">By Month</div>
            <div class="tab" data-target="itemSection">By Item</div>
            <div class="tab" data-target="detailsSection">Order Details</div>
        </div>
        
        <!-- Company Sales Section -->
        <div class="data-section" id="companySection">
            <h2>Sales by Company</h2>
            <div class="chart-container" id="companyChart"></div>
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Company</th>
                        <th>Orders</th>
                        <th>Items Sold</th>
                        <th>Revenue</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($companySales as $company => $data): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($company); ?></td>
                        <td><?php echo $data['orders']; ?></td>
                        <td><?php echo $data['items']; ?></td>
                        <td><?php echo formatCurrency($data['revenue']); ?></td>
                    </tr>
                    <?php endforeach; ?>
                    <?php if (empty($companySales)): ?>
                    <tr>
                        <td colspan="4" style="text-align: center;">No data available</td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        
        <!-- Monthly Sales Section -->
        <div class="data-section" id="monthlySection" style="display: none;">
            <h2>Sales by Month</h2>
            <div class="chart-container" id="monthlyChart"></div>
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Month</th>
                        <th>Revenue</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($monthlySales as $month => $revenue): ?>
                    <tr>
                        <td><?php echo date('F Y', strtotime($month . '-01')); ?></td>
                        <td><?php echo formatCurrency($revenue); ?></td>
                    </tr>
                    <?php endforeach; ?>
                    <?php if (empty($monthlySales)): ?>
                    <tr>
                        <td colspan="2" style="text-align: center;">No data available</td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        
        <!-- Item Sales Section -->
        <div class="data-section" id="itemSection" style="display: none;">
            <h2>Sales by Item</h2>
            <div class="chart-container" id="itemChart"></div>
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Item</th>
                        <th>Quantity Sold</th>
                        <th>Revenue</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($itemSales as $item => $data): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($item); ?></td>
                        <td><?php echo $data['quantity']; ?></td>
                        <td><?php echo formatCurrency($data['revenue']); ?></td>
                    </tr>
                    <?php endforeach; ?>
                    <?php if (empty($itemSales)): ?>
                    <tr>
                        <td colspan="3" style="text-align: center;">No data available</td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        
        <!-- Order Details Section -->
        <div class="data-section" id="detailsSection" style="display: none;">
            <h2>Order Details</h2>
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Job Order #</th>
                        <th>Date</th>
                        <th>Company</th>
                        <th>Item</th>
                        <th>Quantity</th>
                        <th>Price</th>
                        <th>Total</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    // Reset the result pointer
                    $result->data_seek(0);
                    
                    while ($row = $result->fetch_assoc()): 
                        $total = $row['price'] * $row['quantity'];
                    ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['jobOrderNumber']); ?></td>
                        <td><?php echo htmlspecialchars($row['dateCreated']); ?></td>
                        <td><?php echo htmlspecialchars($row['company']); ?></td>
                        <td><?php echo htmlspecialchars($row['items']); ?></td>
                        <td><?php echo htmlspecialchars($row['quantity']); ?></td>
                        <td><?php echo formatCurrency($row['price']); ?></td>
                        <td><?php echo formatCurrency($total); ?></td>
                        <td><?php echo htmlspecialchars($row['status']); ?></td>
                    </tr>
                    <?php endwhile; ?>
                    <?php if ($result->num_rows == 0): ?>
                    <tr>
                        <td colspan="8" style="text-align: center;">No data available</td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
    
    <script src="https://cdnjs.cloudflare.com/ajax/libs/d3/7.8.5/d3.min.js"></script>
    <script>
        // Tab switching functionality
        document.querySelectorAll('#dataTabs .tab').forEach(tab => {
            tab.addEventListener('click', function() {
                // Hide all sections
                document.querySelectorAll('.data-section').forEach(section => {
                    section.style.display = 'none';
                });
                
                // Remove active class from all tabs
                document.querySelectorAll('#dataTabs .tab').forEach(t => {
                    t.classList.remove('active');
                });
                
                // Show the selected section and make tab active
                document.getElementById(this.dataset.target).style.display = 'block';
                this.classList.add('active');
            });
        });
        
        // Company Chart
        function createCompanyChart() {
            const data = [
                <?php foreach (array_slice($companySales, 0, 10) as $company => $data): ?>
                {
                    company: <?php echo json_encode(htmlspecialchars($company)); ?>,
                    revenue: <?php echo $data['revenue']; ?>
                },
                <?php endforeach; ?>
            ];
            
            if (data.length === 0) return;
            
            // Clear previous chart
            d3.select('#companyChart').html('');
            
            const margin = {top: 20, right: 30, bottom: 90, left: 90},
                width = Math.max(data.length * 80, 600) - margin.left - margin.right,
                height = 300 - margin.top - margin.bottom;
            
            const svg = d3.select('#companyChart')
                .append('svg')
                .attr('width', width + margin.left + margin.right)
                .attr('height', height + margin.top + margin.bottom)
                .append('g')
                .attr('transform', `translate(${margin.left},${margin.top})`);
            
            const x = d3.scaleBand()
                .domain(data.map(d => d.company))
                .range([0, width])
                .padding(0.2);
            
            const y = d3.scaleLinear()
                .domain([0, d3.max(data, d => d.revenue) * 1.1])
                .range([height, 0]);
            
            svg.append('g')
                .attr('transform', `translate(0,${height})`)
                .call(d3.axisBottom(x))
                .selectAll('text')
                .attr('transform', 'translate(-10,0)rotate(-45)')
                .style('text-anchor', 'end');
            
            svg.append('g')
                .call(d3.axisLeft(y).tickFormat(d => '₱' + d3.format(',')(d)));
            
            svg.selectAll('bars')
                .data(data)
                .enter()
                .append('rect')
                .attr('x', d => x(d.company))
                .attr('y', d => y(d.revenue))
                .attr('width', x.bandwidth())
                .attr('height', d => height - y(d.revenue))
                .attr('class', 'bar');
            
            // Add title
            svg.append('text')
                .attr('x', width / 2)
                .attr('y', -5)
                .attr('text-anchor', 'middle')
                .style('font-size', '14px')
                .style('font-weight', 'bold')
                .text('Top 10 Companies by Revenue');
        }
        
        // Monthly Chart
        function createMonthlyChart() {
            const data = [
                <?php foreach ($monthlySales as $month => $revenue): ?>
                {
                    month: <?php echo json_encode(date('M Y', strtotime($month . '-01'))); ?>,
                    revenue: <?php echo $revenue; ?>
                },
                <?php endforeach; ?>
            ];
            
            if (data.length === 0) return;
            
            // Clear previous chart
            d3.select('#monthlyChart').html('');
            
            const margin = {top: 20, right: 30, bottom: 70, left: 90},
                width = Math.max(data.length * 80, 600) - margin.left - margin.right,
                height = 300 - margin.top - margin.bottom;
            
            const svg = d3.select('#monthlyChart')
                .append('svg')
                .attr('width', width + margin.left + margin.right)
                .attr('height', height + margin.top + margin.bottom)
                .append('g')
                .attr('transform', `translate(${margin.left},${margin.top})`);
            
            const x = d3.scaleBand()
                .domain(data.map(d => d.month))
                .range([0, width])
                .padding(0.2);
            
            const y = d3.scaleLinear()
                .domain([0, d3.max(data, d => d.revenue) * 1.1])
                .range([height, 0]);
            
            svg.append('g')
                .attr('transform', `translate(0,${height})`)
                .call(d3.axisBottom(x))
                .selectAll('text')
                .attr('transform', 'translate(-10,0)rotate(-45)')
                .style('text-anchor', 'end');
            
            svg.append('g')
                .call(d3.axisLeft(y).tickFormat(d => '₱' + d3.format(',')(d)));
            
            svg.append('path')
                .datum(data)
                .attr('fill', 'none')
                .attr('stroke', '#45a049')
                .attr('stroke-width', 2)
                .attr('d', d3.line()
                    .x(d => x(d.month) + x.bandwidth()/2)
                    .y(d => y(d.revenue))
                );
            
            svg.selectAll('circles')
                .data(data)
                .enter()
                .append('circle')
                .attr('cx', d => x(d.month) + x.bandwidth()/2)
                .attr('cy', d => y(d.revenue))
                .attr('r', 5)
                .attr('fill', '#4CAF50');
            
            // Add title
            svg.append('text')
                .attr('x', width / 2)
                .attr('y', -5)
                .attr('text-anchor', 'middle')
                .style('font-size', '14px')
                .style('font-weight', 'bold')
                .text('Monthly Sales Trend');
        }
        
        // Item Chart
        function createItemChart() {
            const data = [
                <?php foreach (array_slice($itemSales, 0, 10) as $item => $data): ?>
                {
                    item: <?php echo json_encode(htmlspecialchars($item)); ?>,
                    quantity: <?php echo $data['quantity']; ?>,
                    revenue: <?php echo $data['revenue']; ?>
                },
                <?php endforeach; ?>
            ];
            
            if (data.length === 0) return;
            
            // Clear previous chart
            d3.select('#itemChart').html('');
            
            const margin = {top: 20, right: 30, bottom: 90, left: 90},
                width = Math.max(data.length * 80, 600) - margin.left - margin.right,
                height = 300 - margin.top - margin.bottom;
            
            const svg = d3.select('#itemChart')
                .append('svg')
                .attr('width', width + margin.left + margin.right)
                .attr('height', height + margin.top + margin.bottom)
                .append('g')
                .attr('transform', `translate(${margin.left},${margin.top})`);
            
            const x = d3.scaleBand()
                .domain(data.map(d => d.item))
                .range([0, width])
                .padding(0.2);
            
            const y = d3.scaleLinear()
                .domain([0, d3.max(data, d => d.quantity) * 1.1])
                .range([height, 0]);
            
            svg.append('g')
                .attr('transform', `translate(0,${height})`)
                .call(d3.axisBottom(x))
                .selectAll('text')
                .attr('transform', 'translate(-10,0)rotate(-45)')
                .style('text-anchor', 'end');
            
            svg.append('g')
                .call(d3.axisLeft(y).tickFormat(d => d3.format(',')(d)));
            
            svg.selectAll('bars')
                .data(data)
                .enter()
                .append('rect')
                .attr('x', d => x(d.item))
                .attr('y', d => y(d.quantity))
                .attr('width', x.bandwidth())
                .attr('height', d => height - y(d.quantity))
                .attr('class', 'bar');
            
            // Add title
            svg.append('text')
                .attr('x', width / 2)
                .attr('y', -5)
                .attr('text-anchor', 'middle')
                .style('font-size', '14px')
                .style('font-weight', 'bold')
                .text('Top 10 Items by Quantity Sold');
        }
        
        // Function to export data to CSV
        function exportToCSV() {
            // Get table data from the active section
            let activeSection = document.querySelector('.data-section:not([style*="display: none"])');
            let table = activeSection.querySelector('.data-table');
            let rows = table.querySelectorAll('tr');
            
            let csvContent = "data:text/csv;charset=utf-8,";
            
            // Get headers
            let headers = [];
            rows[0].querySelectorAll('th').forEach(th => {
                headers.push('"' + th.textContent.trim() + '"');
            });
            csvContent += headers.join(',') + '\n';
            
            // Get data rows
            for (let i = 1; i < rows.length; i++) {
                let row = [];
                rows[i].querySelectorAll('td').forEach(td => {
                    row.push('"' + td.textContent.trim() + '"');
                });
                csvContent += row.join(',') + '\n';
            }
            
            // Create download link
            let encodedUri = encodeURI(csvContent);
            let link = document.createElement("a");
            link.setAttribute("href", encodedUri);
            link.setAttribute("download", "sales_report.csv");
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
        }
        
        // Initialize charts when the page loads
        document.addEventListener('DOMContentLoaded', function() {
            createCompanyChart();
            createMonthlyChart();
            createItemChart();
        });
    </script>
</body>
</html>