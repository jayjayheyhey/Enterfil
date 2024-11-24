const signUpButton=document.getElementById('signUpButton');
const signUpForm=document.getElementById('signup');
const signInButton=document.getElementById('signInButton');
const signInForm=document.getElementById('signIn');
const dashboard=document.getElementById('dashboard');
const addItemButton=document.getElementById('addItemButton');
const addInterface = document.getElementById('addInterface');
const enterFilterCode=document.getElementById('enterFilterCode');
const submitFilterCodeButton=document.getElementById('submitFilterCodeButton');
const updateQuantity=document.getElementById('updateQuantity');


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
        if (addInterface) {
            addInterface.style.display = "block";
        }
    });
}

if (submitFilterCodeButton) {
    submitFilterCodeButton.addEventListener('click', function () {
        enterFilterCode.style.display = "none";
        updateQuantity.style.display = "block";
    });
}