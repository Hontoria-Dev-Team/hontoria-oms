const imageBox = document.getElementById('imageBox');
const imageBoxCloseButton = document.getElementById('imageBoxCloseButton');
const imageBoxImage = document.getElementById('imageBoxImage');
const imageBoxBackground = document.getElementById('imageBoxBackground');

imageBoxCloseButton.addEventListener('click', function () {
    imageBox.style.display = 'none';
});
imageBoxBackground.addEventListener('click', function () {
    imageBox.style.display = 'none';
});
