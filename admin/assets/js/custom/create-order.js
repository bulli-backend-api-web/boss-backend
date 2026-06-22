"use strict";

var KTB2COrderDetails = function () {
    var form, validator, submitButton;

    return {
        init: function () {
            form = document.getElementById("kt_create_order_form");
            submitButton = document.getElementById("kt_order_details_submit");
            validator = FormValidation.formValidation(form, {
                fields: {
                    channel: {
                        validators: {
                            notEmpty: { message: "Please select channel" },
                        },
                    },
                    store_id: {
                        validators: {
                            callback: {
                                message: 'Please Select store name',
                                callback: function(input) {
                                    let channel = $('input[name="channel"]:checked').val();
                                    if (channel && channel.toLowerCase() === 'store') {
                                        return input.value.trim() !== '';
                                    }
                                    return true;
                                }
                            }
                        }
                    },
                    whole_saler_id: {
                        validators: {
                            callback: {
                                message: 'Please Select Wholesaler name',
                                callback: function(input) {
                                    let channel = $('input[name="channel"]:checked').val();
                                    if (channel && channel.toLowerCase() === 'wholesale') {
                                        return input.value.trim() !== '';
                                    }
                                    return true;
                                }
                            }
                        }
                    },
                    fullname: {
                        validators: {
                            notEmpty: { message: "Please enter customer name" },
                        },
                    },
                    
                    cmobile : {
                        validators: {
                            notEmpty: { message: "Please enter Mobile Number" },
                            regexp: {
                                regexp: /^[0-9]{10}$/,
                                message: "Enter a valid 10-digit number",
                            },
                        },
                    },
                    'product_id[]': {
                        validators: {
                            notEmpty: { message: "Please select product" },
                        },
                    },
                    pincode: {
                        validators: {
                            notEmpty: { message: "Please enter Pincode" },
                        },
                    },
                    country_id: {
                        validators: {
                            notEmpty: { message: "Please Select Country" },
                        },
                    },
                    state_id: {
                        validators: {
                            notEmpty: { message: "Please Select State" },
                        },
                    },
                    city: {
                        validators: {
                            notEmpty: { message: "Please Enter city Name" },
                        },
                    },
                    size: {
                        validators: {
                            notEmpty: { message: "Please select Size" },
                        },
                    },
                    delivery_date : {
                        validators: {
                            notEmpty: { message: "Please select Delivery Date" },
                        },
                    },
                    shipping_address : {
                        validators: {
                            notEmpty: { message: "Please enter shipping address" },
                        },
                    },
                    amount : {
                        validators: {
                            notEmpty: { message: "Please enter amount" },
                        },
                    }
                },

                plugins: {
                    trigger: new FormValidation.plugins.Trigger(),
                    bootstrap5: new FormValidation.plugins.Bootstrap5({
                        rowSelector: ".fv-row",
                        eleInvalidClass: "",
                        eleValidClass: "",
                    }),
                    submitButton: new FormValidation.plugins.SubmitButton(),
                    // ❌ removed DefaultSubmit so page won’t reload
                },
            });

            

            // On submit button click
            submitButton.addEventListener("click", function (e) {
                e.preventDefault();
               
                validator.validate().then(function (status) {
                    if (status === "Valid") {
                        submitButton.setAttribute("data-kt-indicator", "on");
                        submitButton.disabled = true;
                        submitButton.innerHTML = 'Please wait...';
                        let form1 = document.getElementById("kt_create_order_form");
                        let finalData = new FormData(form1);
                        finalData.append('action','add-order');
                        $.ajax({
                            url: form.getAttribute('action'),
                            type: "POST",
                            data: finalData,
                            processData: false,
                            contentType: false,
                            dataType: "json",
                            success: function (response) {
                                submitButton.removeAttribute("data-kt-indicator");
                                submitButton.disabled = false;
                                submitButton.innerHTML = 'Submit';

                                if(response.status === "success") {
                                Swal.fire({
                                        text: response.message,
                                    icon: "success",
                                    buttonsStyling: false,
                                    confirmButtonText: "Ok, got it!",
                                        customClass: { confirmButton: "btn btn-primary" }
                                    }).then(() => {
                                        form.reset();
                                        // Optional: reload page or refresh table
                                        location.reload();
                                });
                                } else {
                                    Swal.fire({
                                        text: response.message || "Something went wrong.",
                                        icon: "error",
                                        buttonsStyling: false,
                                        confirmButtonText: "Ok, got it!",
                                        customClass: { confirmButton: "btn btn-primary" }
                                    });
                                }
                            },
                            error: function () {
                                submitButton.removeAttribute("data-kt-indicator");

                                Swal.fire({
                                    text: "Something went wrong. Please try again.",
                                    icon: "error",
                                    buttonsStyling: false,
                                    confirmButtonText: "Ok, got it!",
                                    customClass: { confirmButton: "btn btn-danger" },
                                });
                            },
                        });
                    } else {
                        Swal.fire({
                            text: "Sorry, please fix the errors in the form.",
                            icon: "error",
                            buttonsStyling: false,
                            confirmButtonText: "Ok, got it!",
                            customClass: { confirmButton: "btn btn-primary" },
                        });
                    }
                });
            });
        },
    };
}();

// Init on DOM load
KTUtil.onDOMContentLoaded(function () {
    KTB2COrderDetails.init();
});
