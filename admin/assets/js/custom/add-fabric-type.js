"use strict";
var KTaddFabricType = function() {
    const t = document.getElementById("kt_modal_add_fabric_type"),
        e = t.querySelector("#kt_modal_add_fabric_type_form"),
        n = new bootstrap.Modal(t);
    return {
        init: function() {
            (() => {
                var o = FormValidation.formValidation(e, {
                    fields: {
                        fabric_type: {
                            validators: {
                                notEmpty: {
                                    message: "Name is required"
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

                const i = t.querySelector('[data-kt-fabric-type-modal-action="submit"]');

                i.addEventListener("click", t => {
                    t.preventDefault();
                    o && o.validate().then(function(result) {
                        if (result === "Valid") {
                            i.setAttribute("data-kt-indicator", "on");
                            i.disabled = true;
                            const formData = new FormData(e); 
                            formData.append("action", "add-fabric-type");
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
                t.querySelector('[data-kt-fabric-type-modal-action="cancel"]').addEventListener("click", ev => {
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
                t.querySelector('[data-kt-fabric-type-modal-action="close"]').addEventListener("click", ev => {
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
    KTaddFabricType.init();
});
