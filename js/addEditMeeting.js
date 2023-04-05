// Grab all DOM id elems
const selectedMeeting = JSON.parse(localStorage.getItem('selectedMeeting'));
const meetingForm = document.getElementById('meeting-form');
const formTitle = document.getElementById('form-title');
const addEditMeetingbtn = document.getElementById('add-edit-meeting-btn');

// Empty meeting to handle new or updated meeting
const meeting = {};

// Event listener for form submit
meetingForm.addEventListener('submit', (e) => submitMeeting(e));

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
        document.getElementById('data').value = selectedMeeting.data;
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

// Log the selected meeting object (if present)
console.log('Selected Meeting:', selectedMeeting);