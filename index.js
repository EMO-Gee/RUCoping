let slideIndex=1;
showSlides(slideIndex);

//onclick of next icon invoke showSlides function
function plusSlides(n){
    showSlides(slideIndex+=n)
}

//onclick of dot icon invoke showSlides function
function currentSlide(n){
    showSlides(slideIndex=n);
}


function showSlides(n){
    let i;
    let slides=document.getElementsByClassName("mySlide");
    let dots = document.getElementsByClassName("dot");
    if(n>slides.length){slideIndex=1;} // slide greater than slide length return to first slide
    if(n<1){slideIndex=slides.length}// slides less that 1 then retune last slide
    for(i=0;i<slides.length;i++){
        slides[i].style.display ="none" // disable all all slides
    }
    for(i=0;i<dots.length;i++){
        dots[i].className = dots[i].className.replace("active"," "); // disable display all dots
    }

    slides[slideIndex-1].style.display ="block";  // display slide
    dots[slideIndex-1].className += "active"; // change dot 
}

if(navigator.vibrate){ // if the vibrate navigator is enabled on click call button vibrates
    document.getElementsById("SOS").addEventListener("click",()=>window.navigator.vibrate(200));}








/*

function adminReply(){
    localStorage.set("newNotification","You have a new message");
}
    


window.onload = function(){

    const notificationMsg = localStorage.getItem(newNotification);

    if(notificationMsg){ 

      const bell = document.getElementsByClassName("notification");
      const noBell = document.getElementsByClassName("no-notification");  
      bell.style.display = "block";
      noBell.style.display = "none";
    }
}

document.getElementsByClassName("notification").addEventListener("click",function()
{
    const msg = localStorage.getItem("newNotification");

    if(msg){
        alert(msg);
        
        localStorage.removeItem("newNotification");
        document.document.getElementsByClassName("notification").style.display = "none";
        document.getElementsByClassName("no-notification").style.display = "block";
    }

})
*/

//-----------------------------------------------------------------------------//
//                    Changing Article Theme                                   //
//-----------------------------------------------------------------------------//

document.addEventListener('DOMContentLoaded', () => {
    const toggle = document.getElementById('dark-mode-toggle');
    const body = document.body;

    // 1. Check saved preference from localStorage
    if (localStorage.getItem('theme') === 'dark') {
        body.classList.add('dark-mode');
        toggle.checked = true; // sync toggle
    }

    // 2. Listen for toggle change
    toggle.addEventListener('change', () => {
        if (toggle.checked) {
            body.classList.add('dark-mode');
            localStorage.setItem('theme', 'dark');
        } else {
            body.classList.remove('dark-mode');
            localStorage.setItem('theme', 'light');
        }
    });
});   
    
   
    
