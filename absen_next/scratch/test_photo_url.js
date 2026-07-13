const https = require('https');

const paths = [
  'https://mawar.smartpesantren.id/sekretariat/berkas/',
  'https://mawar.smartpesantren.id/berkas/',
  'https://mawar.smartpesantren.id/sekretariat/uploads/',
  'https://mawar.smartpesantren.id/uploads/',
  'https://mawar.smartpesantren.id/sekretariat/foto/',
  'https://mawar.smartpesantren.id/foto/',
  'https://mawar.smartpesantren.id/api_absensi/berkas/',
  'https://mawar.smartpesantren.id/api_absensi/foto/',
  'https://mawar.smartpesantren.id/sekretariat/berkas_murid/',
  'https://mawar.smartpesantren.id/berkas_murid/',
  'https://mawar.smartpesantren.id/sekretariat/assets/berkas/',
  'https://mawar.smartpesantren.id/assets/berkas/',
  'https://mawar.smartpesantren.id/sekretariat/media/',
  'https://mawar.smartpesantren.id/media/'
];

const filename = 'Berkas_2023_2023080001.jpg';

const headers = {
  'User-Agent': 'PostmanRuntime/7.36.3',
  'Accept': 'image/avif,image/webp,image/apng,image/svg+xml,image/*,*/*;q=0.8',
  'Referer': 'https://mawar.smartpesantren.id/'
};

console.log(`Testing paths for filename ${filename}...`);

paths.forEach((basePath) => {
  const url = `${basePath}${filename}`;
  const req = https.request(url, { method: 'HEAD', headers }, (res) => {
    console.log(`Path: ${basePath} -> Status: ${res.statusCode}`);
  }).on('error', (err) => {
    console.error(`Error for ${basePath}:`, err.message);
  });
  req.end();
});
