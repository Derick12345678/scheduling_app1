const calendar = document.querySelector("#app-calendar");
const prevMonthButton = document.querySelector("#prev-month");
const nextMonthButton = document.querySelector("#next-month");
const currentMonthYear = document.querySelector("#current-month-year");
const dayInfo = document.querySelector("#day-info");

const daysOfWeek = ["Sun", "Mon", "Tue", "Wed", "Thu", "Fri", "Sat"];
const months = [
    "January", "February", "March", "April", "May", "June",
    "July", "August", "September", "October", "November", "December"
];

let currentMonth = new Date().getMonth();
let currentYear = new Date().getFullYear();


function fetchAppointments(date) {
    $.ajax({
        url: 'api.php', 
        method: 'GET',
        dataType: 'json', 
        data: { date: date },
        success: function (response) {
            try {
                const data = Object.values(response);
                const timeSlots = document.querySelectorAll("#time-slots .time-slot");
                timeSlots.forEach(slot => {
                    const time = slot.querySelector("span").textContent;
                    const appointment = data.find(app => app.time.startsWith(time));
                    if (appointment) {
                        slot.querySelector(".task").textContent = `${appointment.client_name} - ${appointment.description}`;
                    } else {
                        slot.querySelector(".task").textContent = "No tasks";
                    }
                });
            } catch (error) {
                console.error("Error:", error);
            }
        },
        error: function (xhr, status, error) {
            console.error("AJAX Error:", error);
        }
    });
}

function clearCalendar() {
    calendar.innerHTML = "";
}

function renderDayCalendar(day, month, year) {
    const date = `${year}-${String(month + 1).padStart(2, "0")}-${String(day).padStart(2, "0")}`;
    dayInfo.innerHTML = `
        <h3>${day} ${months[month]} ${year}</h3>
        <div id="time-slots">
            ${Array.from({ length: 10 }, (_, i) => {
                const time = `${8 + i}:00`;
                return `
                    <div class="time-slot">
                        <span>${time}</span>
                        <div class="task">Poop</div>
                    </div>`;
            }).join("")}
        </div>
    `;
    fetchAppointments(date);
}


function renderCalendar(month, year) {
    clearCalendar();

    currentMonthYear.textContent = `${months[month]} ${year}`;

    daysOfWeek.forEach(day => {
        calendar.insertAdjacentHTML("beforeend", `<div class="day-name">${day}</div>`);
    });

    const firstDay = new Date(year, month, 1).getDay();
    const daysInMonth = new Date(year, month + 1, 0).getDate();

    const totalCells = 42;

    for (let i = 0; i < firstDay; i++) {
        calendar.insertAdjacentHTML("beforeend", `<div class="day empty"></div>`);
    }

    // Add days of the month
    for (let day = 1; day <= daysInMonth; day++) {
        const weekend = (firstDay + day - 1) % 7 === 0 || (firstDay + day - 1) % 7 === 6;
        const dayElement = document.createElement("div");
        dayElement.classList.add("day", weekend ? "weekend" : "none");
        dayElement.textContent = day;

        // Add click event for day details
        dayElement.addEventListener("click", () => {
            renderDayCalendar(day, month, year);
        });

        calendar.appendChild(dayElement);
    }

    // Add blank cells for remaining grid space after the last day of the month
    const filledCells = firstDay + daysInMonth;
    for (let i = filledCells; i < totalCells; i++) {
        calendar.insertAdjacentHTML("beforeend", `<div class="day empty"></div>`);
    }
}

// Event listeners for navigation buttons
prevMonthButton.addEventListener("click", () => {
    if (currentMonth === 0) {
        currentMonth = 11;
        currentYear--;
    } else {
        currentMonth--;
    }
    renderCalendar(currentMonth, currentYear);
});

nextMonthButton.addEventListener("click", () => {
    if (currentMonth === 11) {
        currentMonth = 0;
        currentYear++;
    } else {
        currentMonth++;
    }
    renderCalendar(currentMonth, currentYear);
});

// Initialize the calendar
renderCalendar(currentMonth, currentYear);

