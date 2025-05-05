export const init = () => {
    document.querySelectorAll('.delete-event').forEach(el => {
        el.addEventListener('click', e => {
            if (!confirm('Are you sure you want to delete this event?')) {
                e.preventDefault();
            }
        });
    });
};
