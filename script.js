/*
 * Script.js untuk Soal 2a
 * Bertugas mengambil (fetch) data dari api.php dan mem-parsing-nya ke HTML
 */

// Menjalankan kode setelah semua elemen HTML selesai dimuat
document.addEventListener("DOMContentLoaded", () => {
  // Panggil API backend kita
  fetch("api.php")
    .then((response) => {
      // Jika respons tidak OK (misal: error 500), lempar error
      if (!response.ok) {
        throw new Error("Network response was not ok " + response.statusText);
      }
      // Ubah respons dari teks menjadi objek JSON
      return response.json();
    })
    .then((data) => {
      // --- INI ADALAH PROSES PARSING ---
      // Data JSON sudah ada di variabel 'data'

      // 1. Parsing data statistik (objek tunggal)
      document.getElementById("suhu-max").textContent = data.suhumax;
      document.getElementById("suhu-min").textContent = data.suhumin;
      document.getElementById("suhu-rata").textContent = data.suhurata;

      // 2. Parsing data detail (array)
      const detailContainer = document.getElementById("detail-max");

      // Kosongkan dulu isi container (menghapus "Memuat data...")
      detailContainer.innerHTML = "";

      // Periksa jika array-nya kosong
      if (data.nilai_suhu_max_humid_max.length === 0) {
        detailContainer.innerHTML =
          "<p>Tidak ada data detail yang ditemukan.</p>";
        return;
      }

      // Loop (forEach) setiap item di dalam array 'nilai_suhu_max_humid_max'
      data.nilai_suhu_max_humid_max.forEach((item) => {
        // Buat elemen <p> baru untuk setiap item
        const el = document.createElement("p");

        // Isi elemen <p> dengan data menggunakan template literal
        el.innerHTML = `
                    <b>Timestamp:</b> ${item.timestamp} <br>
                    <b>ID:</b> ${item.idx} | 
                    <b>Suhu:</b> ${item.suhun} &deg;C | 
                    <b>Humid:</b> ${item.humid} % | 
                    <b>Kecerahan:</b> ${item.kecerahan} lux
                `;

        // Masukkan elemen <p> yang sudah diisi ke dalam div container
        detailContainer.appendChild(el);
      });

      // Catatan: Kita tidak menampilkan 'month_year_max' di UI ini
      // tapi kita bisa jika mau, dengan cara yang sama.
    })
    .catch((error) => {
      // Tangkap jika ada error (misal: API mati, JSON rusak)
      console.error("Error saat fetching data:", error);
      document.getElementById(
        "detail-max"
      ).innerHTML = `<p style="color: red;">Gagal memuat data. ${error.message}</p>`;
    });
});
