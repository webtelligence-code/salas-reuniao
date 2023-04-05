// Grab all DOM id elems
const selectedMeeting = JSON.parse(localStorage.getItem('selectedMeeting'));
const meetingForm = document.getElementById('meeting-form');
const formTitle = document.getElementById('form-title');
const addEditMeetingbtn = document.getElementById('add-edit-meeting-btn');

// Empty meeting to handle new or updated meeting
const meeting = {};

const setMinimumDate = () => {
    const dateInput = document.getElementById('data');

    const today = new Date();
    const minDate = today.toLocaleDateString('en-CA', { timeZone: 'Europe/Lisbon', year: 'numeric', month: '2-digit', day: '2-digit' });

    dateInput.setAttribute('min', minDate);
}
setMinimumDate();

const roundToNearestHalfHour = (date) => {
    const minutes = date.getMinutes();
    const roundedMinutes = (minutes < 30 ? 0 : 30);
    date.setMinutes(roundedMinutes);
    date.setSeconds(0);
    return date;
}

// Fucntion that will handle labels for the title and button based on action (add/edit)
const handleDOMLabels = () => {
    // This if statement will handle DOM text labels for add/edit
    let labelText = 'Adicionar Reunião';
    if (selectedMeeting) {
        labelText = 'Editar Reunião';
    }
    document.title = labelText
    formTitle.innerText = labelText
    addEditMeetingbtn.innerText = labelText;
    handleFormData(); // Set data in the forms
}

// Function that will populate the form data inputs
const handleFormData = () => {
    getRooms();
    if (selectedMeeting) {
        document.getElementById('motivo').value = selectedMeeting.motivo;
        //document.getElementById('data').value = selectedMeeting.data;
        document.getElementById('hora_inicio').value = selectedMeeting.hora_inicio;
        document.getElementById('hora_fim').value = selectedMeeting.hora_fim;
    }
}

// Function to get Rooms from the database api
const getRooms = () => {
    $.get('api/index.php?action=get_rooms', (data, status) => {
        if (status === 'success') {
            const parsedData = JSON.parse(data)
            populateRooms(parsedData); // Populate Rooms Select dropdown
        } else {
            console.log('Failed to fetch rooms from api.');
        }
    })
}

/**
 * Function to populate rooms select dropdown
 * @param {Object Array} rooms 
 */
const populateRooms = (rooms) => {
    const roomsSelect = document.getElementById('sala');
    console.log(rooms);
    rooms.map((room) => {
        var option = document.createElement('option');
        option.innerText = room.nome;
        option.value = room.id;

        // If selected meeting isset then match the option
        if (selectedMeeting && selectedMeeting.sala === room.nome) {
            option.selected = true;
        }

        roomsSelect.appendChild(option);
    });
}

// Function to handle edit/add PHP API
const submitMeeting = (e) => {
    e.preventDefault()
    if (selectedMeeting) {
        updateMeeting() // Update the meeting
    } else {
        addMeeting(); // Add new meeting
    }
}

// Function that will handle the meeting update
// It will call PHP API to handle update on database
const updateMeeting = () => {
    console.log('You are going to update the meeting.')
}

// Function that will handle add new meeting
// It will call PHP API to handle insert into database
const addMeeting = () => {
    console.log('You are going to create a new meeting.')
}

handleDOMLabels(); // Call handleDOMLabels

// Event listener for form submit
meetingForm.addEventListener('submit', (e) => submitMeeting(e));

// Set the initial value of the time input to the nearest half-hour
document.addEventListener('DOMContentLoaded', () => {
    const horaInicioInput = document.getElementById('hora_inicio');
    const now = new Date();
    const nearestHalfHour = roundToNearestHalfHour(now);
    const timeString = nearestHalfHour.toISOString().substr(11, 5);
    horaInicioInput.value = timeString;
});

// Log the selected meeting object (if present)
console.log('Selected Meeting:', selectedMeeting);