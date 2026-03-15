// ============================================
// Philippine Location Data
// File: public/js/ph-locations.js
// ============================================

// Naic Barangays (30 total)
const naicBarangays = [
    "Bagong Karsada",
    "Balsahan",
    "Bancaan",
    "Bucana Malaki",
    "Bucana Sasahan",
    "Calubcob",
    "Capt. C. Nazareno (Pob.)",
    "GOMBALZA",
    "Halang",
    "Humbac",
    "Ibayo Estacion",
    "Ibayo Silangan",
    "Kanluran",
    "Labac",
    "Latoria",
    "Mabulo",
    "Makina",
    "Malainen Bago",
    "Malainen Luma",
    "Molino",
    "Munting Mapino",
    "Muzon",
    "Palangue 1",
    "Palangue 2 & 3",
    "Sabang",
    "San Roque",
    "Santulan",
    "Sapa",
    "Timalan Balsahan",
    "Timalan Concepcion"
];


// Cavite Municipalities
const caviteMunicipalities = [
    "Alfonso",
    "Amadeo",
    "Bacoor",
    "Carmona",
    "Cavite City",
    "Dasmariñas",
    "General Emilio Aguinaldo",
    "General Mariano Alvarez (GMA)",
    "General Trias",
    "Imus",
    "Indang",
    "Kawit",
    "Magallanes",
    "Maragondon",
    "Mendez",
    "Naic",
    "Noveleta",
    "Rosario",
    "Silang",
    "Tagaytay",
    "Tanza",
    "Ternate",
    "Trece Martires"
];

// Philippine Provinces (for completeness)
const philippineProvinces = [
    "Abra", "Agusan del Norte", "Agusan del Sur", "Aklan", "Albay", "Antique",
    "Apayao", "Aurora", "Basilan", "Bataan", "Batanes", "Batangas", "Benguet",
    "Biliran", "Bohol", "Bukidnon", "Bulacan", "Cagayan", "Camarines Norte",
    "Camarines Sur", "Camiguin", "Capiz", "Catanduanes", "Cavite", "Cebu",
    "Cotabato", "Davao de Oro", "Davao del Norte", "Davao del Sur", "Davao Occidental",
    "Davao Oriental", "Dinagat Islands", "Eastern Samar", "Guimaras", "Ifugao",
    "Ilocos Norte", "Ilocos Sur", "Iloilo", "Isabela", "Kalinga", "La Union",
    "Laguna", "Lanao del Norte", "Lanao del Sur", "Leyte", "Maguindanao",
    "Marinduque", "Masbate", "Metro Manila", "Misamis Occidental", "Misamis Oriental",
    "Mountain Province", "Negros Occidental", "Negros Oriental", "Northern Samar",
    "Nueva Ecija", "Nueva Vizcaya", "Occidental Mindoro", "Oriental Mindoro",
    "Palawan", "Pampanga", "Pangasinan", "Quezon", "Quirino", "Rizal", "Romblon",
    "Samar", "Sarangani", "Siquijor", "Sorsogon", "South Cotabato", "Southern Leyte",
    "Sultan Kudarat", "Sulu", "Surigao del Norte", "Surigao del Sur", "Tarlac",
    "Tawi-Tawi", "Zambales", "Zamboanga del Norte", "Zamboanga del Sur",
    "Zamboanga Sibugay"
];

// Helper function to populate a select element
function populateSelect(selectId, dataArray, defaultValue = null) {
    const select = document.getElementById(selectId);
    if (!select) return;
    
    // Clear existing options
    select.innerHTML = '<option value="">-- Select --</option>';
    
    // Add options
    dataArray.forEach(item => {
        const option = document.createElement('option');
        option.value = item;
        option.textContent = item;
        if (defaultValue && item === defaultValue) {
            option.selected = true;
        }
        select.appendChild(option);
    });
}

// Initialize on page load
document.addEventListener('DOMContentLoaded', function() {
    // Auto-populate if elements exist
    if (document.getElementById('barangay')) {
        populateSelect('barangay', naicBarangays, 'Bancaan');
    }
    if (document.getElementById('municipality')) {
        populateSelect('municipality', caviteMunicipalities, 'Naic');
    }
    if (document.getElementById('province')) {
        populateSelect('province', philippineProvinces, 'Cavite');
    }
});