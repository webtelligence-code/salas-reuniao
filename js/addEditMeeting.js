// Grab all DOM id elems
const selectedMeeting = JSON.parse(localStorage.getItem('selectedMeeting'));
const meetingForm = document.getElementById('meeting-form');
const formTitle = document.getElementById('form-title');
const addEditMeetingbtn = document.getElementById('add-edit-meeting-btn');
const cancelBtn = document.getElementById('cancel-btn');

const setMinimumDate = () => {
    const dateInput = document.getElementById('data');

    const today = new Date();
    const minDate = today.toLocaleDateString('en-CA', { timeZone: 'Europe/Lisbon', year: 'numeric', month: '2-digit', day: '2-digit' });

    dateInput.setAttribute('min', minDate);
}
setMinimumDate();

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
    getUsers();
    if (selectedMeeting) {
        document.getElementById('motivo').value = selectedMeeting.motivo;
        document.getElementById('data').value = selectedMeeting.data;
        document.getElementById('hora_inicio').value = selectedMeeting.hora_inicio;

        const startTime = new Date(`${selectedMeeting.data}T${selectedMeeting.hora_inicio}`);
        const endTime = new Date(`${selectedMeeting.data}T${selectedMeeting.hora_fim}`);
        const duration = (endTime - startTime) / 60000 // Duration in minutes

        // Set the duration to the dropdown
        const durationSelect = document.getElementById('duration');
        for (let i = 0; i < durationSelect.options.length; i++) {
            if (parseInt(durationSelect.options[i].value) === duration) {
                durationSelect.selectedIndex = i;
                break;
            }
        }
    }
}

// Function to get Rooms from the database api
const getRooms = async () => {
    $.get('api/index.php?action=get_rooms', (data, status) => {
        if (status === 'success') {
            const parsedData = JSON.parse(data)
            populateRooms(parsedData); // Populate Rooms Select dropdown
        } else {
            console.log('Failed to fetch rooms from api.');
        }
    })
}

// Function that will call api to fetch all users from database
const getUsers = async () => {
    $.get('api/index.php?action=get_users', (data, status) => {
        if (status === 'success') {
            const parsedData = JSON.parse(data);
            populateUsers(parsedData);
        } else {
            console.log('Failed to fetch guests from api')
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

/**
 * This function will populate users dropdown select
 * @param {array object} users 
 */
const populateUsers = (users) => {
    const usersSelect = document.getElementById('users');
    console.log(users);
    users.map((user) => {
        var option = document.createElement('option');
        option.innerText = user.NAME;
        option.value = user.NAME

        usersSelect.appendChild(option)
    });

    // Initialize Select2
    $('#users').select2({
        theme: "bootstrap-5",
        width: $(this).data('width') ? $(this).data('width') : $(this).hasClass('w-100') ? '100%' : 'style',
        placeholder: $(this).data('placeholder'),
        closeOnSelect: false,
        allowClear: true
    });
}

// Function to handle edit/add PHP API
const submitMeeting = (e) => {
    e.preventDefault();

    // Get the form data
    const motivo = document.getElementById('motivo').value;
    const data = document.getElementById('data').value;
    const hora_inicio = document.getElementById('hora_inicio').value;
    const duration = parseInt(document.getElementById('duration').value);
    const sala = document.getElementById('sala').value;

    // Calculate hora_fim
    const startTime = new Date(`${data}T${hora_inicio}`);
    const [hours, minutes] = startTime.toISOString().substr(11, 5).split(':');
    let endTimeHours = parseInt(hours) + Math.floor((parseInt(minutes) + duration) / 60) + Math.floor(duration / 60);
    let endTimeMinutes = (parseInt(minutes) + duration) % 60;
    const hora_fim = `${endTimeHours.toString().padStart(2, '0')}:${endTimeMinutes.toString().padStart(2, '0')}`;

    // Prepare the meeting object
    const meeting = { motivo, data, hora_inicio, hora_fim, sala };

    if (selectedMeeting) {
        updateMeeting(meeting) // Update the meeting
    } else {
        addMeeting(meeting); // Add new meeting
    }
}

// Function that will handle the meeting update
// It will call PHP API to handle update on database
const updateMeeting = (meeting) => {
    console.log('You are going to update the meeting ->', meeting)
}

// Function that will handle add new meeting
// It will call PHP API to handle insert into database
const addMeeting = (meeting) => {
    console.log('You are going to create a new meeting ->', meeting)
}

const cancelAddEditMeeting = () => {
    history.back()
}

handleDOMLabels(); // Call handleDOMLabels

/**
 * This function will calculate the nearest half hour to hora_inicio value
 * @param {date object} date 
 * @returns next date
 */
const roundToNearestHalfHour = (date) => {
    const minutes = date.getMinutes();
    const roundedMinutes = (minutes < 30 ? 0 : 30);
    date.setMinutes(roundedMinutes);
    date.setSeconds(0);
    return date;
}

// Set the initial value of the time input to the nearest half-hour
document.addEventListener('DOMContentLoaded', () => {
    const horaInicioInput = document.getElementById('hora_inicio');
    const now = new Date();
    const nearestHalfHour = roundToNearestHalfHour(now);
    const timeString = nearestHalfHour.toISOString().substr(11, 5);
    horaInicioInput.value = timeString;
});

// Event listener for form submit
meetingForm.addEventListener('submit', (e) => submitMeeting(e));

// Event listener for cancel button
cancelBtn.addEventListener('click', () => cancelAddEditMeeting())

// Log the selected meeting object (if present)
console.log('Selected Meeting:', selectedMeeting);