let selectedDate = '';
let selectedDesk = null;
let escritoriosOcupados = []; // Inicializar como un array vacío

document.addEventListener('DOMContentLoaded', function() {
    const calendarContainer = document.getElementById('reservation-calendar');
    const timeInput = document.getElementById('reservation-time');
    const confirmTime = document.getElementById('confirm-time');
    const today = new Date().toISOString().split('T')[0];

    // Configuramos Flatpickr para mostrar el calendario siempre visible
    flatpickr(calendarContainer, {
        minDate: today,
        inline: true,
        onChange: function(selectedDates, dateStr) {
            selectedDate = dateStr; // Guardar la fecha seleccionada
            document.getElementById('confirm-date').innerText = dateStr;
        }
    });

    // Validar la hora entre 7:00 AM y 9:30 PM
    timeInput.addEventListener('change', function() {
        const selectedTime = timeInput.value;
        const [hour, minutes] = selectedTime.split(':').map(Number);
        
        if ((hour === 7 && minutes >= 0) || (hour === 21 && minutes <= 30) || (hour > 7 && hour < 21)) {
            confirmTime.innerText = selectedTime;
        } else {
            alert('Por favor selecciona una hora entre las 7:00 PM y 9:30 PM.');
            timeInput.value = ''; // Resetear el valor si no está en el rango
        }
    });
});


function enviarReservaYObtenerEscritorios() {
    const selectedLab = document.getElementById('lab-select').value;
    const selectedDate = document.getElementById('confirm-date').innerText;
    const selectedTime = document.getElementById('confirm-time').innerText;
    const selectedDesk = document.getElementById('confirm-desk').innerText.split(' ')[1]; // Obtenemos el número de escritorio

    if (!selectedLab || !selectedDate || !selectedTime || !selectedDesk) {
        alert('Por favor, completa todos los detalles de la reserva.');
        return;
    }

    const reservaData = {
        lab: selectedLab,
        date: selectedDate,
        time: selectedTime,
        desk: selectedDesk
    };

    fetch('reservasEstu.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify(reservaData)
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            console.log('Reserva confirmada:', data.message);
            obtenerEscritoriosOcupados();
        } else {
            alert(data.message);
        }
    })
    .catch(error => console.error('Error:', error));
}


// Función para obtener los escritorios ocupados después de confirmar la reserva
function obtenerEscritoriosOcupados() {
    fetch('reservasEstu.php')
        .then(response => response.json())
        .then(data => {
            escritoriosOcupados = data;
            generateWorkspaces(selectedLab);
        })
        .catch(error => console.error('Error fetching data:', error));
}

let selectedLab = 0; // Por defecto, Laboratorio 1

function selectLab(labNumber) {
    // Al seleccionar un laboratorio, hacer una solicitud para obtener los espacios disponibles
    fetch('reservasEstu.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({ lab: labNumber })  // Enviar el número de laboratorio seleccionado
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Llamar a la función que genera los escritorios con los espacios obtenidos
            generateWorkspaces(data.espacios);
        } else {
            console.error('Error al obtener los espacios.');
        }
    })
    .catch(error => console.error('Error al hacer la solicitud:', error));
}


function generateWorkspaces(espacios) {
    const workspaceGrid = document.getElementById('workspace-grid');
    workspaceGrid.innerHTML = ''; // Limpiar los espacios anteriores

    // Crear los escritorios en función de los datos recibidos
    espacios.forEach(espacio => {
        const workspace = document.createElement('div');
        workspace.classList.add('workspace');
        workspace.textContent = `Espacio ${espacio.espacio_id}`;
        
        // Comprobar si el espacio está ocupado
        if (!espacio.activa) {
            workspace.classList.add('occupied');  // Marcar como ocupado si no está activo
        } else {
            workspace.onclick = function() {
                selectDesk(espacio.espacio_id);
            };
        }

        workspaceGrid.appendChild(workspace); // Añadir el espacio al contenedor
    });
}


function selectDesk(deskNumber) {
    // Remover la clase 'selected' de todos los escritorios
    document.querySelectorAll('.workspace').forEach((desk) => {
        desk.classList.remove('selected');
    });

    // Marcar el escritorio seleccionado
    const selected = document.querySelector(`.workspace:nth-child(${deskNumber})`);
    selected.classList.add('selected');

    // Actualizar el panel de confirmación
    document.getElementById('confirm-desk').innerText = `Espacio ${deskNumber}`;
}


// Función para pasar a la siguiente fase y actualizar la sección de confirmación
function nextStep(step) {
    document.querySelectorAll('.step-content').forEach((section) => {
        section.style.display = 'none';
    });
    document.querySelectorAll('.step-content1').forEach((section) => {
        section.style.display = 'none';
    });

    // Muestra la sección correspondiente
    document.getElementById('step' + step).style.display = 'flex';

    // Actualiza los pasos completados visualmente
    document.querySelectorAll('.step').forEach((el, idx) => {
        if (idx < step - 1) {
            el.classList.add('completed');
        } else {
            el.classList.remove('completed');
        }
    });

    // Si estamos en la fase de confirmación (step 3), actualizamos los detalles
    if (step === 3) {
        // Actualizamos la información de confirmación
        const selectedLabText = document.querySelector('#lab-select option:checked').textContent;
        document.getElementById('confirm-lab').innerText = selectedLabText;
    }
}


// Función para retroceder al paso anterior
function previousStep(step) {
    nextStep(step); // Mostrar el paso anterior

    // Restaurar los valores seleccionados cuando el usuario regresa
    if (step === 1) {
        if (selectedDate) {
            document.getElementById('confirm-date').innerText = selectedDate;
        }
        if (selectedTime) {
            const timeInput = document.getElementById('reservation-time');
            timeInput.value = selectedTime; // Restaurar la hora visualmente
            document.getElementById('confirm-time').innerText = selectedTime;
        }
    } else if (step === 2) {
        if (selectedLab) {
            document.getElementById('lab-select').value = selectedLab;
            generateWorkspaces(selectedLab); // Regenerar los espacios

            // Restaurar el escritorio seleccionado si existe
            if (selectedDesk) {
                selectDesk(selectedDesk); // Restaurar el escritorio seleccionado
            }
        }
    }
}



document.querySelector('.confirm-btn').addEventListener('click', function() {
    // Recopilar los datos seleccionados
    const selectedLab = document.querySelector('#lab-select').value;
    const selectedDate = document.getElementById('confirm-date').innerText;
    const selectedTime = document.getElementById('confirm-time').innerText;
    const selectedDesk = document.getElementById('confirm-desk').innerText.split(' ')[1];  // Obtenemos el número de espacio
    const companionEmail = document.getElementById('companion-email') ? document.getElementById('companion-email').value : null;

    // Nombre del encargado, lo puedes obtener de un campo o dejarlo estático por ahora
    const encargado = 'NombreEncargado';  // Esto debería ser dinámico según tus datos, por ejemplo, del login del usuario.
    const activa = true;

    // Verifica que todos los datos requeridos estén presentes
    if (!selectedLab || !selectedDate || !selectedTime || !selectedDesk) {
        alert('Por favor, completa todos los detalles de la reserva.');
        return;
    }

    // Crear un objeto con los datos de la reserva
    const reservaData = {
        lab: selectedLab,
        date: selectedDate,
        time: selectedTime,
        desk: selectedDesk,
        encargado: encargado,
        activa: activa,
        companion: companionEmail || null  // Si el email está vacío, lo enviamos como null
    };

    // Enviar los datos al servidor
    fetch('reservasEstu.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify(reservaData)
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Reserva confirmada');
            console.log(reservaData);  // Verifica que los datos están correctos
            obtenerEscritoriosOcupados();  // Para actualizar los espacios ocupados
        } else {
            alert('Error: ' + data.message);
        }
    })
    .catch(error => console.error('Error:', error));
    

});
