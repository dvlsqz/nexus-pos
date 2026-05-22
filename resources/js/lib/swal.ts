import Swal from 'sweetalert2';

export const swal = Swal.mixin({
    buttonsStyling: true,
    customClass: {
        confirmButton: 'rounded-lg px-4 py-2 text-sm font-medium',
        cancelButton: 'rounded-lg px-4 py-2 text-sm font-medium',
        popup: 'rounded-xl shadow-lg',
    },
});

export default swal;