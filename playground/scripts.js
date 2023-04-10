const populateHoraInicioDropdown = () => {
    const horaInicioSelect = document.getElementById('hora_inicio');
    let currentTime = new Date();
    currentTime.setHours(0, 0, 0, 0);

    while (currentTime.getDate() === 1) {
        const timeString = currentTime.toISOString().substr(11, 5);

        const option = document.createElement('option');
        option.innerText = timeString;
        option.value = timeString;

        horaInicioSelect.appendChild(option);
        currentTime.setMinutes(currentTime.getMinutes() + 30);
    }
};


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

    // Add event listener for the change event
    roomsSelect.addEventListener('change', (e) => {
        const selectedRoomId = e.target.value;
        handleRoomChange(selectedRoomId);
    });
};

const handleRoomChange = async (selectedRoomId) => {
    const date = document.getElementById('data').value;

    if (!date) {
        return;
    }

    const blockedTimeSlots = await getBlockedTimeSlots(selectedRoomId, date);
    const horaInicioSelect = document.getElementById('hora_inicio');

    // Clear the existing options
    while (horaInicioSelect.firstChild) {
        horaInicioSelect.removeChild(horaInicioSelect.firstChild);
    }

    populateHoraInicio(blockedTimeSlots);
};

const getBlockedTimeSlots = async (id_sala, data) => {
    const response = await $.get('api/index.php', {
        action: 'get_blocked_time_slots',
        id_sala,
        data
    });

    return JSON.parse(response);
};


