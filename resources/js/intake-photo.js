function initIntakePhotoPreview() {
    const photoInput = document.getElementById('photo');
    const preview = document.getElementById('photo-preview');

    if (! photoInput || ! preview) {
        return;
    }

    photoInput.addEventListener('change', () => {
        const file = photoInput.files?.[0];

        if (! file) {
            if (preview.dataset.defaultSrc) {
                preview.src = preview.dataset.defaultSrc;
                preview.classList.remove('intake-photo-preview-empty');
            }

            return;
        }

        const reader = new FileReader();

        reader.onload = (event) => {
            if (preview.tagName === 'IMG') {
                preview.src = event.target?.result;
                preview.classList.remove('intake-photo-preview-empty');
            } else {
                const image = document.createElement('img');
                image.id = 'photo-preview';
                image.className = 'intake-photo-preview';
                image.alt = '';
                image.src = event.target?.result ?? '';
                preview.replaceWith(image);
            }
        };

        reader.readAsDataURL(file);
    });
}

document.addEventListener('DOMContentLoaded', initIntakePhotoPreview);
