/*
 * Script.js untuk Soal 2a
 */

// Menjalankan kode setelah semua elemen HTML selesai dimuat
document.addEventListener("DOMContentLoaded", () => {
  fetch("api.php")
    .then((response) => {
      if (!response.ok) {
        throw new Error("Network response was not ok " + response.statusText);
      }
      return response.json();
    })
    .then((data) => {
      // 1. Parsing data statistik (objek tunggal)
      document.getElementById("suhu-max").textContent = data.suhumax;
      document.getElementById("suhu-min").textContent = data.suhumin;
      document.getElementById("suhu-rata").textContent = data.suhurata;

      // 2. Parsing data detail (array)
      const detailContainer = document.getElementById("detail-max");

      detailContainer.innerHTML = "";

      if (data.nilai_suhu_max_humid_max.length === 0) {
        detailContainer.innerHTML =
          "<p>Tidak ada data detail yang ditemukan.</p>";
        return;
      }

      data.nilai_suhu_max_humid_max.forEach((item) => {
        const el = document.createElement("p");

        el.innerHTML = `
                    <b>Timestamp:</b> ${item.timestamp} <br>
                    <b>ID:</b> ${item.idx} | 
                    <b>Suhu:</b> ${item.suhun} &deg;C | 
                    <b>Humid:</b> ${item.humid} % | 
                    <b>Kecerahan:</b> ${item.kecerahan} lux
                `;

        detailContainer.appendChild(el);
      });
    })
    .catch((error) => {
      console.error("Error saat fetching data:", error);
      document.getElementById(
        "detail-max"
      ).innerHTML = `<p style="color: red;">Gagal memuat data. ${error.message}</p>`;
    });
});
