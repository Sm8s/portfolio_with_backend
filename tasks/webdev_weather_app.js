/*
 * Weather App (Client‑Side)
 *
 * Dieses Beispiel zeigt, wie du mit der Fetch API eine Wetter‑API
 * aufrufen könntest, um aktuelle Wetterdaten abzurufen. Ersetze
 * `YOUR_API_KEY` und `CITY_NAME` mit deinen eigenen Werten. Die
 * Temperatur wird im Anschluss an die Konsolenausgabe angezeigt. In einem
 * vollständigen Projekt würdest du diese Daten im DOM darstellen.
 */

async function getWeather(city) {
  const apiKey = 'YOUR_API_KEY';
  const url = `https://api.openweathermap.org/data/2.5/weather?q=${city}&units=metric&appid=${apiKey}`;
  try {
    const response = await fetch(url);
    if (!response.ok) {
      throw new Error('Konnte Wetterdaten nicht abrufen');
    }
    const data = await response.json();
    console.log(`Wetter in ${city}: ${data.main.temp}°C, ${data.weather[0].description}`);
    return data;
  } catch (error) {
    console.error(error);
    throw error;
  }
}

// Beispielaufruf:
// getWeather('Berlin');
