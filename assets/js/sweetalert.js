function errDialog(title, subtitle, msg = '') {
    Swal.fire({
        icon: "error",
        title: title,
        text: subtitle,
        footer: msg
    });
}

function success(title, reload = true) {
    Swal.fire({
        position: "top",
        icon: "success",
        title: title,
        showConfirmButton: false,
        timer: 1500
    });
    if (reload) {
        setInterval(() => {
            location.reload()
        }, 1500)
    }
}
function confirmDialog(title, text) {
    return Swal.fire({
        title: title,
        text: text,
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#3085d6",
        cancelButtonColor: "#d33",
        confirmButtonText: "ตกลง",
        cancelButtonText: "ยกเลิก"
    })
    // .then((result) => {
    //     if (result.isConfirmed) {
    //         Swal.fire({
    //             title: "Deleted!",
    //             text: "Your file has been deleted.",
    //             icon: "success"
    //         });
    //     }
    // });
}