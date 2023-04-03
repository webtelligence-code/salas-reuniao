let sessionUsername;

// Meetings array
const meetings = [];

// Grab root container that will be populated with the meetings
const meetingsContainer = document.getElementById('meetings-container');

// Grab theloading overlay div
const loadingOverlay = document.getElementById('loading-overlay');

const getSessionUsername = () => {
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
    loadingOverlay.style.display = 'block'; // Show loading overlay
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
        divRow.className = 'row align-items-center p-2 m-3 cardRow'; // Add required classes

        // Condition to verify if session USERNAME matches meeting row 
        const showEditDeleteButtons = sessionUsername === meeting.organizador;

        // If true, show edit/delete buttons
        const editDeleteButtons = showEditDeleteButtons ? `
            <div class='col-12 col text-center'>
                <button id='edit-meeting-${index}' class='btn btn-primary'>Edit</button>
                <button id='delete-meeting-${index}' class='btn btn-danger'>Delete</button>
            </div>
        ` : '';

        // Populate row inner HTML
        divRow.innerHTML = `
            <div class='col-12 col-md-4 text-center'>
                <img class='rounded-circle' src='${meeting.url_imagem}' height='75px'/>
            </div>
            <div class='col-12 col-md-4 text-center'>
                <div class='bold-text'>${meeting.hora_inicio} - ${meeting.hora_fim}</div>
                <div class='sup-text'>${meeting.data}</div>
            </div>
            <div class='col-12 col-md-4 text-center'>
                <div class='bold-text'>${meeting.motivo}</div>
                <div class='sup-text'>${meeting.sala}</div>
            </div>
            ${editDeleteButtons}
        `;
        meetingsContainer.appendChild(divRow); // Append row HTML to parent container

        loadingOverlay.style.display = 'none'; // Hide loading overlay

        // Condition to add event listener (if aplicable) for each edit/delete button
        if (showEditDeleteButtons) {
            const editBtn = document.getElementById(`edit-meeting-${index}`);
            const deleteBtn = document.getElementById(`delete-meeting-${index}`);

            editBtn.addEventListener('click', () => goToEditMeetingPage(meeting));
            deleteBtn.addEventListener('click', () => deleteMeeting(meeting));
        }
    });
}

/**
 * Function that will navigate to addEditMeeting page and pass meeting object to localstorage to grab
 * meeting object and populate the other page form with the correct data.
 * @param {object} meeting 
 */
const goToEditMeetingPage = (meeting) => {
    document.getElementById('loading-overlay').style.display = 'block' // Show spinner
    localStorage.setItem('selectedMeeting', JSON.stringify(meeting)); // Save meeting oobject to local storage
    window.location.href = 'addEditMeeting.php'; // Navigate to edit meeting
}

/**
 * Function that will handle the delete meeting
 * @param {object} meeting 
 */
const deleteMeeting = (meeting) => {
    console.log('You are going to delete ->', meeting)
}