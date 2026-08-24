//-----------------------------------------------------------------------------//
//                    Code for all articles pages                              //
//-----------------------------------------------------------------------------//

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
//-----------------------------------------------------------------------------//
//   Function to shows if you are offline/online and what device is used       //
//-----------------------------------------------------------------------------//

let statusBubble = null;

function showStatusBubble(message, color, duration) {
    // Reuse bubble if it already exists
    if (!statusBubble) {
        statusBubble = document.createElement("div");
        statusBubble.style.position = "fixed";
        statusBubble.style.top = "20px";
        statusBubble.style.left = "50%";
        statusBubble.style.transform = "translateX(-50%)";
        statusBubble.style.padding = "15px 20px";
        statusBubble.style.borderRadius = "10px";
        statusBubble.style.boxShadow = "0 4px 15px rgba(0,0,0,0.2)";
        statusBubble.style.color = "#fff";
        statusBubble.style.zIndex = "10000";
        statusBubble.style.transition = "opacity 1s ease";
        document.body.appendChild(statusBubble);
    }

    // Update content and color
    statusBubble.textContent = message;
    statusBubble.style.backgroundColor = color;
    statusBubble.style.opacity = "1";

    // Clear any existing fade timeout
    if (statusBubble.fadeTimeout) clearTimeout(statusBubble.fadeTimeout);

    // Fade out after duration
    statusBubble.fadeTimeout = setTimeout(() => {
        statusBubble.style.opacity = "0";
        statusBubble.removeTimeout = setTimeout(() => {
            // remove the bubble and reset reference
            statusBubble.remove();
            statusBubble = null;
        }, 1000); // match the CSS fade
    }, duration);
}

//        Function to update body background and show bubble if offline     //
function updateBodyBackground() {
    const body = document.body;
    if (!navigator.onLine) {
        body.style.backgroundColor = "gray"; // offline backgroundColor
        //showStatusBubble("You are offline!", "#B22222", 6000); // red bubble
    }else {
        //body.style.backgroundColor = "#F1E3F3";
        //showStatusBubble("You are online!", "#28a745", 6000); // green bubble
    }
}
//                   Detect if you on a mobile device                      //
// function isMobileDevice() {
//     if (/Mobi|Android/i.test(navigator.userAgent)) {
//         return showStatusBubble("You are on a mobile device!", "#007bff", 2000); // blue bubble
//     }
//     return showStatusBubble("You are on a desktop device!", "#17a2b8", 2000); // cyan bubble
// }

// Run once on page load
//updateBodyBackground();
//isMobileDevice();

// Listen for network changes
//window.addEventListener("online", updateBodyBackground);
//window.addEventListener("offline", updateBodyBackground);

//----------------------------------------------------------------------------//
//                              Ribbon                                        //
//----------------------------------------------------------------------------//
const canvas = document.getElementsByClassName('ribbon')[0];

// Get the current page URL
const fullURL = window.location.href;

// pick color for current page
let ribbonColor;
switch (true) {
    case fullURL.includes("HIV.html"):
        ribbonColor = "#ff3b3f"; // red
        break;
    case fullURL.includes("MentalHealth.html"):
        ribbonColor = "#00B140"; // green
        break;
    case fullURL.includes("assault.html"):
        ribbonColor = "purple"; // purple
        break;
    case fullURL.includes("academics.html"):
        ribbonColor = "#4169E1"; // Royal blue
        break;
    default:
        ribbonColor = "#1E90FF"; // default blue
        break;
}
if(canvas){
    const ctx = canvas.getContext('2d');
    canvas.width = window.innerWidth;
    canvas.height = 600;

    let time = 0;

    // Ribbon settings
    const ribbon = {
    width: 20,
    segments: 100,
    amplitude: 20, //how much the ribbon waves up and down
    wavelength: 2, //how many waves across the screen
    color: ribbonColor
    };

    function drawRibbon() {
    ctx.clearRect(0, 0, canvas.width, canvas.height);

    ctx.beginPath();
    for (let i = 0; i <= ribbon.segments; i++) {
        let t = i / ribbon.segments;
        let x = t * canvas.width;
        let y = canvas.height / 2 + Math.sin(t * Math.PI * ribbon.wavelength + time) * ribbon.amplitude; // create a sin wave effect
        if (i === 0) ctx.moveTo(x, y);
        else ctx.lineTo(x, y);
    }
    ctx.lineWidth = ribbon.width;
    ctx.lineCap = 'round';
    ctx.strokeStyle = ribbon.color;
    ctx.stroke();

    time += 0.02;
    requestAnimationFrame(drawRibbon);
    }

    drawRibbon();

    // Get the canvas to resize with the window
    window.addEventListener('resize', () => {
    canvas.width = window.innerWidth;
    canvas.height = window.innerHeight;
    });
}

//-----------------------------------------------------------------------------//
//                        Cards Transtion in-and-out                           //
//-----------------------------------------------------------------------------//
const cards = document.querySelectorAll('.card');

function checkCards() {
    const triggerBottom = window.innerHeight * 0.9; // trigger a bit before fully visible

    cards.forEach(card => {
        const cardTop = card.getBoundingClientRect().top;

        if(cardTop < triggerBottom){
            card.classList.add('show');
        }
    });
}
window.addEventListener('scroll', checkCards);
window.addEventListener('load', checkCards);
//-----------------------------------------------------------------------------//
//                        Run the main functions                               //
//-----------------------------------------------------------------------------//

// Run once on page load
//updateBodyBackground();
//isMobileDevice();

// Listen for network changes
//window.addEventListener("online", updateBodyBackground);
//window.addEventListener("offline", updateBodyBackground);

