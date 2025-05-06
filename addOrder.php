<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="addOrderInterface2.css">
    <title>Add Filter</title>
</head>
<body>
<div class="container" id="addOrderInterface">
        <a href="#" class="back-btn"><i class="fas fa-arrow-left"></i>
        </a>
        <h1 class="form-title">Add Order</h1>
        
        <form method="post" action="addOrder.php" enctype="multipart/form-data">
            <div class="form-row">
                <div class="form-col">
                    <div class="input-group">
                        <i class="fas fa-hashtag"></i>
                        <input type="number" name="jobOrderNumber" id="jobOrderNumber" placeholder="Job Order Number" required>
                        <label for="jobOrderNumber">Job Order No.</label>
                    </div>
                    <div class="input-group">
                        <i class="fas fa-building"></i>
                        <input type="text" name="company" id="company" placeholder="Company" required>
                        <label for="company">Company Name</label>
                    </div>
                    <div class="input-group">
                        <i class="fas fa-boxes"></i>
                        <input type="text" name="items" id="items" placeholder="Items" required>
                        <label for="items">Items</label>
                    </div>
                </div>
                
                <div class="form-col">
                    <div class="input-group">
                        <i class="fas fa-sort-numeric-up"></i>
                        <input type="number" name="quantity" id="quantity" placeholder="Quantity" required>
                        <label for="quantity">Order Quantity</label>
                    </div>
                    <div class="input-group">
                        <i class="fas fa-calendar-alt"></i>
                        <input type="date" name="requiredDate" id="requiredDate" placeholder="Required Date" required>
                        <label for="requiredDate">Date Required</label>
                    </div>
                </div>
            </div>
            
            <div class="divider"></div>
            <div class="section-title">Order Information</div>
            
            <div class="form-row">
                <div class="form-col">
                    <!-- Modified Cap input to place dropdown beside the input -->
                    <div class="input-with-dropdown">
                        <div class="input-group">
                            <i class="fas fa-cog"></i>
                            <input type="text" name="cap" id="cap" placeholder="Cap" required>
                            <label for="cap">Cap</label>
                        </div>
                        <select name="capUOM" id="capUOM">
                            <option value="mm">mm</option>
                            <option value="cm">cm</option>
                            <option value="in">in</option>
                        </select>
                    </div>

                    <div class="input-with-dropdown">
                        <div class="input-group">
                            <i class="fas fa-ruler"></i>
                            <input type="text" name="size" id="size" placeholder="Size" required>
                            <label for="size">Size</label>
                        </div>
                        <select name="capUOM" id="capUOM">
                            <option value="mm">mm</option>
                            <option value="cm">cm</option>
                            <option value="in">in</option>
                        </select>
                    </div>


                    <div class="input-with-dropdown">
                        <div class="input-group">
                            <i class="fas fa-dot-circle"></i>
                            <input type="text" name="gasket" id="gasket" placeholder="Gasket" required>
                            <label for="gasket">Gasket</label>
                        </div>
                        <select name="capUOM" id="capUOM">
                            <option value="mm">mm</option>
                            <option value="cm">cm</option>
                            <option value="in">in</option>
                        </select>
                    </div>
                    
                    <div class="input-with-dropdown">
                        <div class="input-group">
                            <i class="fas fa-ring"></i>
                            <input type="text" name="oring" id="oring" placeholder="O-Ring" required>
                            <label for="oring">O-Ring</label>
                        </div>
                        <select name="capUOM" id="capUOM">
                            <option value="mm">mm</option>
                            <option value="cm">cm</option>
                            <option value="in">in</option>
                        </select>
                    </div>
                    


                </div>
                
                <div class="form-col">
                    <div class="input-group">
                        <i class="fas fa-filter"></i>
                        <input type="text" name="filterMedia" id="filterMedia" placeholder="Filter Media" required>
                        <label for="filterMedia">Filter Media</label>
                    </div>
                    <div class="input-group">
                        <i class="fas fa-arrow-down"></i>
                        <input type="text" name="insideSupport" id="insideSupport" placeholder="Inside Support" required>
                        <label for="insideSupport">Inside Support</label>
                    </div>
                    <div class="input-group">
                        <i class="fas fa-arrow-up"></i>
                        <input type="text" name="outsideSupport" id="outsideSupport" placeholder="Outside Support" required>
                        <label for="outsideSupport">Outside Support</label>
                    </div>
                    <div class="input-group">
                        <i class="fas fa-tag"></i>
                        <input type="text" name="brand" id="brand" placeholder="Brand" required>
                        <label for="brand">Brand</label>
                    </div>
                </div>
            </div>
            
            <div class="bottom-row">
                <div class="price-container">
                    <div class="input-group">
                        <i class="fas fa-money-bill"></i>
                        <input type="number" step="0.01" name="price" id="price" placeholder="Price (₱)" required>
                        <label for="price">Price</label>
                    </div>
                </div>
                
                <button type="submit" class="done-btn" name="submitButton">Mark as Draft</button>
            </div>
        </form>
    </div>
</body>
</html>