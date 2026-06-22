"use strict";
var KTUsersUpdateDetails = function() {
    const t = document.getElementById("kt_modal_update_details"),
        e = t.querySelector("#kt_modal_update_user_form"),
        n = new bootstrap.Modal(t);

    return {
        init: function() {
            (() => {
                // Cancel / Close buttons
                t.querySelector('[data-kt-users-modal-action="close"]').addEventListener("click", cancelHandler);
                t.querySelector('[data-kt-users-modal-action="cancel"]').addEventListener("click", cancelHandler);

                function cancelHandler(ev) {
                    ev.preventDefault();
                    Swal.fire({
                        text: "Are you sure you would like to cancel?",
                        icon: "warning",
                        showCancelButton: true,
                        buttonsStyling: false,
                        confirmButtonText: "Yes, cancel it!",
                        cancelButtonText: "No, return",
                        customClass: {
                            confirmButton: "btn btn-primary",
                            cancelButton: "btn btn-active-light"
                        }
                    }).then(function(result) {
                        if (result.value) {
                            e.reset();
                            n.hide();
                        } else if (result.dismiss === "cancel") {
                            Swal.fire({
                                text: "Your form has not been cancelled!",
                                icon: "error",
                                buttonsStyling: false,
                                confirmButtonText: "Ok, got it!",
                                customClass: {
                                    confirmButton: "btn btn-primary"
                                }
                            });
                        }
                    });
                }

                // Submit button with AJAX
                const o = t.querySelector('[data-kt-users-modal-action="submit"]');
                o.addEventListener("click", function(ev) {
                    ev.preventDefault();

                    o.setAttribute("data-kt-indicator", "on");
                    o.disabled = true;

                    // Prepare form data
                    let formData = new FormData(e);
                    formData.append("action", "update_user_details"); // optional extra param

                    // AJAX call
                    fetch(e.getAttribute('action'), {
                        method: "POST",
                        body: formData
                    })
                    .then(response => response.json())
                    .then(data => {
                        o.removeAttribute("data-kt-indicator");
                        o.disabled = false;

                        if (data.status === "success") {
                            Swal.fire({
                                text: data.message,
                                icon: "success",
                                buttonsStyling: false,
                                confirmButtonText: "Ok, got it!",
                                customClass: { confirmButton: "btn btn-primary" }
                            }).then(function(result) {
                                if (result.isConfirmed) {
                                    n.hide();
                                    e.reset();
                                    window.location.reload();
                                }
                            });
                        } else {
                            Swal.fire({
                                text: data.message,
                                icon: "error",
                                buttonsStyling: false,
                                confirmButtonText: "Ok, got it!",
                                customClass: { confirmButton: "btn btn-primary" }
                            });
                        }
                    })
                    .catch(error => {
                        o.removeAttribute("data-kt-indicator");
                        o.disabled = false;
                        Swal.fire({
                            text: "Something went wrong: " + error,
                            icon: "error",
                            buttonsStyling: false,
                            confirmButtonText: "Ok, got it!",
                            customClass: { confirmButton: "btn btn-primary" }
                        });
                    });
                });

            })()
        }
    }
}();

KTUtil.onDOMContentLoaded(function() {
    KTUsersUpdateDetails.init();
});
