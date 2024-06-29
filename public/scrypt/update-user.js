document.addEventListener("DOMContentLoaded", () => {
    const data =
    {
        image: '',
    }
    const imageInput = document.getElementById('avatar_path');

    function onAvatarPathInputChange(event) {
        const file = event.target.files[0];
        readFileAsBase64(file, (result) => {
            data.image = result;
            invalidateAvatarPathPreview()
        });
        document.querySelectorAll('.block__upload-new-button')[0].classList.add('block__upload-new-button_download')
        document.querySelectorAll('.block__remove-button')[0].classList.add('block__remove-button_download')
    }

    function readFileAsBase64(file, onload) {
        const reader = new FileReader();
        reader.addEventListener("load", () => {
            onload(reader.result);
        }, false,);
        reader.readAsDataURL(file);
    }

    function initEventListener() {
        imageInput.addEventListener('change', onAvatarPathInputChange)
        document.querySelectorAll('.block__upload-new-button')[0].classList.add('block__upload-new-button_download')
        document.querySelectorAll('.block__remove-button')[0].classList.add('block__remove-button_download')
    }

    function invalidateAvatarPathPreview() {
        const PreviewAvatarPath = document.querySelector('.block__avatar-preview')
        PreviewAvatarPath.src = data.image
    }

    initEventListener();

    document.querySelectorAll('.block__remove-button')[0].onclick = deleteImage;

    function deleteImage() {
        const PreviewAvatarPath = document.querySelector('.block__avatar-preview')
        PreviewAvatarPath.src = "/image/avatar.png";
        document.querySelectorAll('.block__upload-new-button')[0].classList.remove('block__upload-new-button_download')
        document.querySelectorAll('.block__remove-button')[0].classList.remove('block__remove-button_download')
    }
});