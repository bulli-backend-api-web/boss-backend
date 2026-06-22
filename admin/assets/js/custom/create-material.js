"use strict";

var KTCreateMaterial = function () {
    var form, validator, submitButton;

    var bindValidation = function () {
        if (validator) {
            validator.destroy();
        }

        validator = FormValidation.formValidation(form, {
            fields: {
                material_name: {
                    validators: {
                        notEmpty: { message: "Material name is required" },
                        stringLength: { max: 150, message: "Material name must be under 150 characters" }
                    }
                },
               
                unit: {
                    validators: {
                        notEmpty: { message: "Material type is required" }
                    }
                }
            },
            plugins: {
                trigger: new FormValidation.plugins.Trigger(),
                bootstrap5: new FormValidation.plugins.Bootstrap5({
                    rowSelector: ".fv-row",
                    eleInvalidClass: "",
                    eleValidClass: ""
                }),
                submitButton: new FormValidation.plugins.SubmitButton()
            }
        });
    };

    var bindSubmit = function () {
        submitButton.addEventListener("click", function (e) {
            e.preventDefault();

            validator.validate().then(function (status) {
                if (status !== "Valid") {
                    Swal.fire({
                        text: "Please fill in all required fields correctly.",
                        icon: "error",
                        buttonsStyling: false,
                        confirmButtonText: "Ok, got it!",
                        customClass: { confirmButton: "btn btn-primary" }
                    });
                    return;
                }

                submitButton.disabled = true;
                submitButton.setAttribute("data-kt-indicator", "on");

                var formData = new FormData(form);

                $.ajax({
                    url: form.getAttribute("action"),
                    type: "POST",
                    data: formData,
                    processData: false,
                    contentType: false,
                    dataType: "json",
                    success: function (response) {
                        submitButton.removeAttribute("data-kt-indicator");
                        submitButton.disabled = false;

                        if (response.status === "success") {
                            Swal.fire({
                                text: response.message || "Material saved successfully!",
                                icon: "success",
                                buttonsStyling: false,
                                confirmButtonText: "Ok, got it!",
                                customClass: { confirmButton: "btn btn-primary" }
                            }).then(function () {
                                window.location.href = document.getElementById("redirect_page").value;
                            });
                        } else {
                            Swal.fire({
                                text: response.message || "Something went wrong, please try again.",
                                icon: "error",
                                buttonsStyling: false,
                                confirmButtonText: "Ok, got it!",
                                customClass: { confirmButton: "btn btn-primary" }
                            });
                        }
                    },
                    error: function () {
                        submitButton.removeAttribute("data-kt-indicator");
                        submitButton.disabled = false;

                        Swal.fire({
                            text: "Server error, please try again later.",
                            icon: "error",
                            buttonsStyling: false,
                            confirmButtonText: "Ok, got it!",
                            customClass: { confirmButton: "btn btn-primary" }
                        });
                    }
                });
            });
        });
    };

    return {
        init: function () {
            form = document.getElementById("kt_create_material_form");
            if (!form) {
                return;
            }

            // Hard stop against double-binding (duplicate script include,
            // duplicate init call) — this is what causes error messages or
            // the AJAX submit to fire twice.
            if (form.dataset.fvBound === "1") {
                return;
            }
            form.dataset.fvBound = "1";

            submitButton = document.getElementById("kt_create_material_details_submit");

            bindValidation();
            bindSubmit();
        }
    };
}();

KTUtil.onDOMContentLoaded(function () {
    KTCreateMaterial.init();
});