console.log("JS loaded");
document.getElementById("date").addEventListener("change",updateSlots);
document.getElementById("counsellor").addEventListener("change",updateSlots);

function updateSlots(){
    let date = document.getElementById("date").value;// obtain chosen date;
    let counsellor = document.getElementById("counsellor").value;// obtain chosen counsellor/counsellors;

    if(date=== "" || counsellor=== ""){
        return;
    }

    // fetch available time slots for the chosen date and counsellor from the server using an AJAX request
    fetch("Appointment_timeSlots.php?date=" + encodeURIComponent(date) + "&counsellor=" + encodeURIComponent(counsellor)) // fetch available counsellors and time 
    .then(response => response.json()) // convert fetched datat to Json format
    .then(data => {
        let availableTimes= document.getElementById("slot_time");
        availableTimes.innerHTML = "<option>Select a time</option>";

    data.forEach(slot => { // create an <option> element for a drop down list from available time slots
        let option = document.createElement("option");
        option.value = slot; // value = " " equivalent 
        option.textContent= slot; // actual text

        availableTimes.appendChild(option); // add each slot to drop down list
    });

    .catch(error => {
        console.error("Error fetching slots:", error);
    });
}