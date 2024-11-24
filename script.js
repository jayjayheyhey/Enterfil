const signUpButton=document.getElementById('signUpButton');
const signInButton=document.getElementById('signInButton');
const addItemButton=document.getElementById('addItemButton');
const dashboard=document.getElementById('dashboard');
const addInterface = document.getElementById('addInterface');
const signInForm=document.getElementById('signIn');
const signUpForm=document.getElementById('signup');


signUpButton.addEventListener('click',function(){
    signInForm.style.display="none";
    signUpForm.style.display="block";
})
signInButton.addEventListener('click', function(){
    signInForm.style.display="block";
    signUpForm.style.display="none";
})

addItem.addEventListener('click', function(){
    signInForm.style.display="block";
    signUpForm.style.display="none";
})

if (addInterface) {
    addInterface.style.display = "none";
}

if (addItemButton) {
    addItemButton.addEventListener('click', function () {     
        // Show the addInterface
        if (addInterface) {
            addInterface.style.display = "block";
        }
    });
}