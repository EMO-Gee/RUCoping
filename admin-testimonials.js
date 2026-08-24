// utility script for admin pages (search, highlight, animations)
// original testimonial management moved to server-side PHP

(function(){
    // Highlight active sidebar link
    document.addEventListener("DOMContentLoaded", () => {
        const links = document.querySelectorAll(".sidebar a");
        const currentPage = window.location.pathname.split("/").pop();

        links.forEach(link => {
            if (link.getAttribute("href") === currentPage) {
                link.classList.add("active");
            }
        });
    });

    // Live search filter (only if input exists)
    const searchInput = document.getElementById("searchInput");
    if (searchInput) {
        searchInput.addEventListener("keyup", () => {
            const filter = searchInput.value.toLowerCase();
            const cards = document.querySelectorAll(".card");

            cards.forEach(card => {
                const text = card.textContent.toLowerCase();
                card.style.display = text.includes(filter) ? "block" : "none";
            });
        });
    }

    document.addEventListener("DOMContentLoaded", () => {
        const cards = document.querySelectorAll(".card");

        cards.forEach((card, index) => {
            card.style.opacity = "0";
            card.style.transform = "translateY(20px)";
            setTimeout(() => {
                card.style.transition = "all 0.4s ease";
                card.style.opacity = "1";
                card.style.transform = "translateY(0)";
            }, index * 150);
        });
    });

})();

//Kganyi
function approveAppointment(appointmentID){
    fetch('approve_appointment.php', {
        method: 'POST',
        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
        body: 'id=' + appointmentID
    })

    .then(response => response.text())
    .then(data => { alert(data);
        document.getElementById('appointment-' + appointmentID).style.display ='none';
    })
    .catch(error => console.error('Error:', error))
}

    // Highlight active sidebar link
    document.addEventListener("DOMContentLoaded", () => {
        const links = document.querySelectorAll(".sidebar a");
        const currentPage = window.location.pathname.split("/").pop();

        links.forEach(link => {
            if (link.getAttribute("href") === currentPage) {
                link.classList.add("active");
            }
        });
    });

    // Live search filter (only if input exists)
    const searchInput = document.getElementById("searchInput");
    if (searchInput) {
        searchInput.addEventListener("keyup", () => {
            const filter = searchInput.value.toLowerCase();
            const cards = document.querySelectorAll(".card");

            cards.forEach(card => {
                const text = card.textContent.toLowerCase();
                card.style.display = text.includes(filter) ? "block" : "none";
            });
        });
    }

    document.addEventListener("DOMContentLoaded", () => {
    const cards = document.querySelectorAll(".card");

});

const sortSelectElem = document.getElementById("sortSelect");
if (sortSelectElem) {
    sortSelectElem.addEventListener("change", function () {
        const cardsContainer = document.querySelector(".main");
        const cards = Array.from(document.querySelectorAll(".card"));
        const order = this.value;

        cards.sort((a, b) => {
            const dateA = new Date(a.querySelector(".date").textContent);
            const dateB = new Date(b.querySelector(".date").textContent);

            return order === "asc" ? dateA - dateB : dateB - dateA;
        });

        cards.forEach(card => cardsContainer.appendChild(card));
    });
}

document.addEventListener("DOMContentLoaded", () => {
    const cards = document.querySelectorAll(".card");

    cards.forEach((card, index) => {
        card.style.opacity = "0";
        card.style.transform = "translateY(20px)";
        setTimeout(() => {
            card.style.transition = "all 0.4s ease";
            card.style.opacity = "1";
            card.style.transform = "translateY(0)";
        }, index * 150);
    });
});

cancelBtn.addEventListener("click", () => {
    if (confirm("Are you sure you want to cancel this appointment?")) {
        card.style.backgroundColor = "#f8d7da";
        card.style.opacity = "0.7";
    }
});


//Kganyi
function approveAppointment(appointmentID){
    fetch('approve_appointment.php', {
        method: 'POST',
        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
        body: 'id=' + appointmentID
    })

    .then(response => response.text())
    .then(data => { alert(data);
        document.getElementById('appointment-' + appointmentID).style.display ='none';
    })
    .catch(error => console.error('Error:', error))
}

