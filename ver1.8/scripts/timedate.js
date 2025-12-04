function gettheDate()
{
    Todays = new Date(); // Pobranie daty
    TheDate = "" + Todays.getDate() + " / " + (Todays.getMonth() + 1) + " / " + (Todays.getYear() - 100); // Wyświetlanie dnia, miesiąca i roku
    document.getElementById("data").innerHTML = TheDate;
}

var timerID = null;
var timerRunning = false;

function stopclock()
{
    if (timerRunning)
        clearTimeout(timerID); // Zatrzymanie zegarka
    timerRunning = false;
}

function startclock()
{
    stopclock(); // Zatrzymanie zegarka
    gettheDate(); // Wyświetlenie daty
    showtime(); // Wyświetlnie czasu
}

function showtime()
{
    var now = new Date(); // Pobranie daty razem z godziną
    var hours = now.getHours(); // Pobranie godziny
    var minutes = now.getMinutes(); // Pobranie minut
    var seconds = now.getSeconds(); // Pobranie sekund
    var timeValue = "" + hours; // Wyświetlanie godziny
    timeValue += ((minutes < 10) ? ":0" : ":") + minutes; //Wyświetlanie minut
    timeValue += ((seconds < 10) ? ":0" : ":") + seconds; // Wyświetlanie sekund
    document.getElementById("zegarek").innerHTML = timeValue;
    timerID = setTimeout("showtime()", 1000);
    timerRunning = true;
}
