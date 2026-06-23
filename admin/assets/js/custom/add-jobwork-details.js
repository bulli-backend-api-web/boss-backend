"use strict";
var KTUaddJobWorkDetails = function() {
    const t = document.getElementById("kt_modal_add_jobwork_type"),
        e = t.querySelector("#kt_modal_add_jobwork_type_form"),
        n = new bootstrap.Modal(t);
    return {
        init: function() {
            (() => {
                var o = FormValidation.formValidation(e, {
                    fields: {
                        jobwork_name: {
                            validators: {
                                notEmpty: {
                                    message: "Jobwork Type is required"
                                }
                            }
                        },
                        amount: {
                            validators: {
                                notEmpty: {
                                    message: "Amount is required"
                                },regexp: {
                                    regexp: /^[0-9]+(\.[0-9]{1,2})?$/,
                                    message: "Enter a valid amount"
                                }
                            }
                        }
                    },
                    plugins: {
                        trigger: new FormValidation.plugins.Trigger,
                        bootstrap: new FormValidation.plugins.Bootstrap5({
                            rowSelector: ".fv-row",
                            eleInvalidClass: "",
                            eleValidClass: ""
                        })
                    }
                });

                const i = t.querySelector('[data-kt-jobwork-modal-action="submit"]');

                i.addEventListener("click", t => {
                    t.preventDefault();
                    o && o.validate().then(function(result) {
                        if (result === "Valid") {
                            i.setAttribute("data-kt-indicator", "on");
                            i.disabled = true;
                            const formData = new FormData(e); 
                            formData.append("action", "add-jobwork");
                            $.ajax({
                                url: e.getAttribute('action'),
                                type: "POST",
                                data: formData,
                                processData: false,
                                contentType: false,
                                dataType: "json",
                                success: function(response) {
                                    i.removeAttribute("data-kt-indicator");
                                    i.disabled = false;

                                    if (response.status === "success") {
                                        Swal.fire({
                                            text: response.message,
                                            icon: "success",
                                            buttonsStyling: !1,
                                            confirmButtonText: "Ok, got it!",
                                            customClass: {
                                                confirmButton: "btn btn-primary"
                                            }
                                        }).then(function(t) {
                                            if (t.isConfirmed) {
                                                e.reset();
                                                n.hide();
                                                window.location.reload();
                                                // Optionally reload table/list here
                                            }
                                        });
                                    } else {
                                        Swal.fire({
                                            text: response.message,
                                            icon: "error",
                                            buttonsStyling: !1,
                                            confirmButtonText: "Ok, got it!",
                                            customClass: {
                                                confirmButton: "btn btn-primary"
                                            }
                                        });
                                    }
                                },
                                error: function(xhr, status, error) {
                                    i.removeAttribute("data-kt-indicator");
                                    i.disabled = false;
                                    Swal.fire({
                                        text: "Something went wrong: " + error,
                                        icon: "error",
                                        buttonsStyling: !1,
                                        confirmButtonText: "Ok, got it!",
                                        customClass: {
                                            confirmButton: "btn btn-primary"
                                        }
                                    });
                                }
                            });
                        } else {
                            Swal.fire({
                                text: "Sorry, looks like there are some errors detected, please try again.",
                                icon: "error",
                                buttonsStyling: !1,
                                confirmButtonText: "Ok, got it!",
                                customClass: {
                                    confirmButton: "btn btn-primary"
                                }
                            });
                        }
                    })
                });

                // Cancel button
                t.querySelector('[data-kt-jobwork-modal-action="cancel"]').addEventListener("click", ev => {
                    ev.preventDefault();
                    Swal.fire({
                        text: "Are you sure you would like to cancel?",
                        icon: "warning",
                        showCancelButton: !0,
                        buttonsStyling: !1,
                        confirmButtonText: "Yes, cancel it!",
                        cancelButtonText: "No, return",
                        customClass: {
                            confirmButton: "btn btn-primary",
                            cancelButton: "btn btn-active-light"
                        }
                    }).then(function(t) {
                        if (t.value) { e.reset(); n.hide(); }
                    })
                });

                // Close button
                t.querySelector('[data-kt-jobwork-modal-action="close"]').addEventListener("click", ev => {
                    ev.preventDefault();
                    Swal.fire({
                        text: "Are you sure you would like to cancel?",
                        icon: "warning",
                        showCancelButton: !0,
                        buttonsStyling: !1,
                        confirmButtonText: "Yes, cancel it!",
                        cancelButtonText: "No, return",
                        customClass: {
                            confirmButton: "btn btn-primary",
                            cancelButton: "btn btn-active-light"
                        }
                    }).then(function(t) {
                        if (t.value) { e.reset(); n.hide(); }
                    })
                });
            })()
        }
    }
}();

KTUtil.onDOMContentLoaded(function() {
    KTUaddJobWorkDetails.init();
});
