"use strict";
var KTUsersUpdatePermission = function() {
    const t = document.getElementById("kt_modal_update_permission"),
        e = t.querySelector("#kt_modal_update_permission_form"),
        n = new bootstrap.Modal(t);
    return {
        init: function() {
            (() => {
                var o = FormValidation.formValidation(e, {
                    fields: {
                        permission_name: {
                            validators: {
                                notEmpty: {
                                    message: "Permission name is required"
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
                t.querySelector('[data-kt-permissions-modal-action="close"]').addEventListener("click", t => {
                    t.preventDefault(), Swal.fire({
                        text: "Are you sure you would like to close?",
                        icon: "warning",
                        showCancelButton: !0,
                        buttonsStyling: !1,
                        confirmButtonText: "Yes, close it!",
                        cancelButtonText: "No, return",
                        customClass: {
                            confirmButton: "btn btn-primary",
                            cancelButton: "btn btn-active-light"
                        }
                    }).then(function(t) {
                        t.value && n.hide()
                    })
                }), t.querySelector('[data-kt-permissions-modal-action="cancel"]').addEventListener("click", t => {
                    t.preventDefault(), Swal.fire({
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
                        t.value ? (e.reset(), n.hide()) : "cancel" === t.dismiss && Swal.fire({
                            text: "Your form has not been cancelled!.",
                            icon: "error",
                            buttonsStyling: !1,
                            confirmButtonText: "Ok, got it!",
                            customClass: {
                                confirmButton: "btn btn-primary"
                            }
                        })
                    })
                });
                const i = t.querySelector('[data-kt-permissions-modal-action="submit"]');
				i.addEventListener("click", function (ev) {
				    ev.preventDefault();

				    o && o.validate().then(function (status) {
				        console.log("validated!");

				        if (status === "Valid") {
				            // show spinner
				            i.setAttribute("data-kt-indicator", "on");
				            i.disabled = true;

				            // get form element
				            let form = document.getElementById("kt_modal_update_permission_form");
				            let formData = new FormData(form);
				            formData.append("action", "update_permission");
				            fetch(e.getAttribute('action'), {
				                method: "POST",
				                body: formData
				            })
				                .then(res => res.json())
				                .then(data => {
				                    // remove spinner
				                    i.removeAttribute("data-kt-indicator");
				                    i.disabled = false;

				                    if (data.status === "success") {
				                        Swal.fire({
				                            text: data.message,
				                            icon: "success",
				                            buttonsStyling: !1,
				                            confirmButtonText: "Ok, got it!",
				                            customClass: { confirmButton: "btn btn-primary" }
				                        }).then(function (result) {
				                            if (result.isConfirmed) {
				                                n.hide(); // close modal
				                                location.reload(); // refresh page (or update DataTable row dynamically)
				                            }
				                        });
				                    } else {
				                        Swal.fire({
				                            text: data.message || "Something went wrong!",
				                            icon: "error",
				                            buttonsStyling: !1,
				                            confirmButtonText: "Ok, got it!",
				                            customClass: { confirmButton: "btn btn-primary" }
				                        });
				                    }
				                })
				                .catch(error => {
				                    i.removeAttribute("data-kt-indicator");
				                    i.disabled = false;

				                    Swal.fire({
				                        text: "AJAX error: " + error,
				                        icon: "error",
				                        buttonsStyling: !1,
				                        confirmButtonText: "Ok, got it!",
				                        customClass: { confirmButton: "btn btn-primary" }
				                    });
				                });
				        } else {
				            Swal.fire({
				                text: "Sorry, looks like there are some errors detected, please try again.",
				                icon: "error",
				                buttonsStyling: !1,
				                confirmButtonText: "Ok, got it!",
				                customClass: { confirmButton: "btn btn-primary" }
				            });
				        }
				    });
				})
            })()
        }
    }
}();
KTUtil.onDOMContentLoaded(function() {
    KTUsersUpdatePermission.init()
});