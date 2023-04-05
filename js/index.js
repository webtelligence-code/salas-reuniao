let sessionUsername;

// Meetings array
const meetings = [];

// Grab the add meeting button id
const addMeetingBtn = document.getElementById('add-meeting-btn');
// Grab root container that will be populated with the meetings
const meetingsContainer = document.getElementById('meetings-container');
// Grab theloading overlay div
const loadingOverlay = document.getElementById('loading-overlay');

addMeetingBtn.addEventListener('click', () => goToAddEditMeetingPage(null));

const getSessionUsername = () => {
    loadingOverlay.style.display = 'block'; // Show loading overlay
    $.get('api/index.php?action=get_username', (data, status) => {
        if (status === 'success') {
            // API call success
            const parsedData = JSON.parse(data)
            console.log('USERNAME =>', parsedData);
            sessionUsername = parsedData
        } else {
            // API call error
            alert('Failed to fetch session username');
        }
    })
}
getSessionUsername(); // Grab the current session USERNAME via ajax call

// Function to call api to fetch all meetings
const getMeetings = () => {
    $.get('api/index.php?action=get_meetings', (data, status) => {
        if (status === 'success') {
            // Api call success
            console.log(data) // Log result
            const parsedData = JSON.parse(data); // Parse json data
            parsedData.forEach(meeting => {
                meetings.push(meeting); // Push parsed meeting data to meetings array
            });
            populateMeetingsContainer() // Populate meetings container
        } else {
            alert('Error fetching api data'); // Error fetching data from api
        }
    })
}
getMeetings(); // Call getMeetings to fetch all meetings available on database

// Function to populate meetings container
const populateMeetingsContainer = () => {
    meetingsContainer.innerHTML = ''; // CLear meetingsContainer inner HTML

    // Loop through meetings array and populate each meeting
    meetings.forEach((meeting, index) => {
        const divRow = document.createElement('div'); // Create div with row class (bootstrap)

        // Condition to verify if session USERNAME matches meeting row 
        const showEditDeleteButtons = sessionUsername === meeting.organizador;

        // Formatted date and time
        const formattedDate = moment(meeting.data).format('DD/MM/YYYY');
        const formattedHoraInicio = moment(meeting.hora_inicio, 'HH:mm:ss').format('HH:mm');
        const formattedHoraFim = moment(meeting.hora_fim, 'HH:mm:ss').format('HH:mm');

        // If true, show edit/delete buttons
        const editDeleteButtons = showEditDeleteButtons ? `
            <div class='col-sm-12 col-md mt-3'>
                <button id='edit-meeting-${index}' class='btn btn-primary'>Editar</button>
                <button id='delete-meeting-${index}' class='btn btn-danger'>Remover</button>
            </div>
        ` : '';

        // Populate row inner HTML
        divRow.innerHTML = `
            <div class='card my-3 c-card'>
                <div class='row g-0 align-items-center'>
                    <div class='col-sm-12 col-md-4 text-center text-md-start'>
                        <img src='${meeting.url_imagem}' class='img-fluid rounded meeting-image'/>
                    </div>
                    <div class='col-sm-12 col-md-8 text-center text-md-start'>
                        <div class='card-body'>
                            <div class='row align-items-center'>
                                <div class='col-sm-12 col-md'>
                                    <h3 class='card-title' style='color: #ed6337'>${meeting.motivo}</h3>
                                    <p class='card-text'><strong>Data:</strong> ${formattedDate}</p>
                                    <p class='card-text'><strong>Hora: </strong>${formattedHoraInicio}h - ${formattedHoraFim}h</p>
                                    <p class='card-text'><strong>Sala:</strong> ${meeting.sala}</p>
                                </div>
                                ${editDeleteButtons}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        `;
        meetingsContainer.appendChild(divRow); // Append row HTML to parent container

        // Condition to add event listener (if aplicable) for each edit/delete button
        if (showEditDeleteButtons) {
            const editBtn = document.getElementById(`edit-meeting-${index}`);
            const deleteBtn = document.getElementById(`delete-meeting-${index}`);

            editBtn.addEventListener('click', () => goToAddEditMeetingPage(meeting));
            deleteBtn.addEventListener('click', () => deleteMeeting(meeting));
        }

        loadingOverlay.style.display = 'none'; // Hide loading overlay
    });
}

/**
 * Function that will navigate to addEditMeeting page and pass meeting object to localstorage to grab
 * meeting object and populate the other page form with the correct data.
 * If no object is passed through this function, it will navigate to addEditMeeting.html
 * to add a new meeting.
 * @param {object} meeting 
 */
const goToAddEditMeetingPage = (meeting) => {
    if (meeting) {
        localStorage.setItem('selectedMeeting', JSON.stringify(meeting)); // Save meeting object to local storage
    } else {
        localStorage.removeItem('selectedMeeting'); // Remove localStorage object set if meeting object is null
    }
    window.location.href = 'addEditMeeting.html'; // Navigate to add/edit meeting
}

/**
 * Function that will handle the delete meeting
 * @param {object} meeting 
 */
const deleteMeeting = (meeting) => {
    alert(`You are going to delete meeting -> ${meeting}`);
}

window.onbeforeunload = () => {
    loadingOverlay.style.display = 'none'; // Hide the loading spinner before leaving page
};