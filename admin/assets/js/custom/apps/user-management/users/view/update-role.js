"use strict";
var KTUsersUpdateRole = function() {
    const t = document.getElementById("kt_modal_update_role"),
        e = t.querySelector("#kt_modal_update_role_form"),
        n = new bootstrap.Modal(t);

    return {
        init: function() {
            (() => {
                // close + cancel buttons (unchanged)
                t.querySelector('[data-kt-users-modal-action="close"]').addEventListener("click", closeHandler);
                t.querySelector('[data-kt-users-modal-action="cancel"]').addEventListener("click", closeHandler);

                // submit button
                const o = t.querySelector('[data-kt-users-modal-action="submit"]');
                o.addEventListener("click", function(ev) {
                    ev.preventDefault();
                    const checkedRoles = document.querySelectorAll(
                        'input[name="user_role[]"]:checked'
                    );

                    let roleIds = [];

                    checkedRoles.forEach(el => {
                        roleIds.push(el.dataset.id);
                    });

                    let typee_id = roleIds.join(',');
                    let formData = new FormData(e);
                    //let typee_id = document.querySelector('input[name="user_role"]:checked').dataset.id;
                    formData.append("action", "update_role");
                    formData.append("typee_id", typee_id);

                    // loading indicator
                    o.setAttribute("data-kt-indicator", "on");
                    o.disabled = true;

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
                                customClass: {
                                    confirmButton: "btn btn-primary"
                                }
                            }).then(function(res) {
                                if (res.isConfirmed) {
                                    n.hide();
                                    e.reset(); // reset form
                                     location.reload();
                                }
                            });
                        } else {
                            Swal.fire({
                                text: data.message,
                                icon: "error",
                                buttonsStyling: false,
                                confirmButtonText: "Ok, got it!",
                                customClass: {
                                    confirmButton: "btn btn-primary"
                                }
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
                            customClass: {
                                confirmButton: "btn btn-primary"
                            }
                        });
                    });
                });

                function closeHandler(ev) {
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
            })();
        }
    }
}();

KTUtil.onDOMContentLoaded(function() {
    KTUsersUpdateRole.init();
});
