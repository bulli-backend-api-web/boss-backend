"use strict";

var KTAppEcommerceSaveProduct = function () {
    const initRepeater = () => {
        $("#kt_ecommerce_add_product_options").repeater({
            initEmpty: false,
            defaultValues: { "text-input": "foo" },
            show: function () {
                $(this).slideDown();
                initSelect2();
            },
            hide: function (e) {
                $(this).slideUp(e);
            }
        });
    };

    const initSelect2 = () => {
        document.querySelectorAll('[data-kt-ecommerce-catalog-add-product="product_option"]').forEach(el => {
            if (!$(el).hasClass("select2-hidden-accessible")) {
                $(el).select2({ minimumResultsForSearch: -1 });
            }
        });
    };

    return {
        init: function () {
            // ✅ Initialize editors
            ["#kt_ecommerce_add_product_description", "#kt_ecommerce_add_product_meta_description"].forEach(selector => {
                let el = document.querySelector(selector);
                if (el) {
                    new Quill(selector, {
                        modules: {
                            toolbar: [
                                [{ header: [1, 2, false] }],
                                ["bold", "italic", "underline"],
                                ["image", "code-block"]
                            ]
                        },
                        placeholder: "Type your text here...",
                        theme: "snow"
                    });
                }
            });

            // ✅ Initialize tags
            ["#kt_ecommerce_add_product_category", "#kt_ecommerce_add_product_tags"].forEach(selector => {
                const el = document.querySelector(selector);
                if (el) {
                    new Tagify(el, {
                        whitelist: ["new", "trending", "sale", "discounted", "selling fast", "last 10"],
                        dropdown: {
                            maxItems: 20,
                            classname: "tagify__inline__suggestions",
                            enabled: 0,
                            closeOnSelect: false
                        }
                    });
                }
            });

            // ✅ Initialize dropzone (if used)
            if (document.querySelector("#kt_ecommerce_add_product_media")) {
                new Dropzone("#kt_ecommerce_add_product_media", {
                    url: "upload.php", // ⚠️ update your upload handler here
                    paramName: "file",
                    maxFiles: 10,
                    maxFilesize: 10,
                    addRemoveLinks: true
                });
            }

            // ✅ Form validation
            const form = document.getElementById("kt_ecommerce_add_product_form");
            const submitBtn = document.getElementById("kt_ecommerce_add_product_submit");

            const validator = FormValidation.formValidation(form, {
                fields: {
                    product_name: {
                        validators: {
                            notEmpty: { message: "Product name is required" }
                        }
                    },
                    sku: {
                        validators: {
                            notEmpty: { message: "SKU is required" }
                        }
                    },
                    barcode: {
                        validators: {
                            notEmpty: { message: "Product barcode is required" }
                        }
                    },
                    shelf: {
                        validators: {
                            notEmpty: { message: "Shelf quantity is required" }
                        }
                    },
                    price: {
                        validators: {
                            notEmpty: { message: "Product base price is required" }
                        }
                    },
                    tax: {
                        validators: {
                            notEmpty: { message: "Product tax class is required" }
                        }
                    }
                },
                plugins: {
                    trigger: new FormValidation.plugins.Trigger(),
                    bootstrap: new FormValidation.plugins.Bootstrap5({
                        rowSelector: ".fv-row",
                        eleInvalidClass: "",
                        eleValidClass: ""
                    })
                }
            });

            // ✅ Submit handler (AJAX)
            submitBtn.addEventListener("click", function (e) {
                e.preventDefault();

                validator.validate().then(function (status) {
                    if (status === "Valid") {
                        submitBtn.setAttribute("data-kt-indicator", "on");
                        submitBtn.disabled = true;
                        $('#hidden_description').val($('#kt_ecommerce_add_product_description').html());

                        // ✅ Collect form data
                        const formData = new FormData(form);
                        formData.append('action','update-product');
                        // ✅ AJAX request
                        $.ajax({
                            url: form.getAttribute('action'),
                            type: "POST",
                            data: formData,
                            processData: false,
                            contentType: false,
                            dataType: "json", 
                            success: function (response) {
                                submitBtn.removeAttribute("data-kt-indicator");
                                if(response.status === "success") {
                                    Swal.fire({
                                        text: response.message,
                                        icon: "success",
                                        buttonsStyling: false,
                                        confirmButtonText: "Ok, got it!",
                                        customClass: { confirmButton: "btn btn-primary" }
                                    }).then(() => {
                                        form.reset();
                                        window.location.reload();
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
                            error: function (xhr) {
                                Swal.fire({
                                    text: "Error saving product. Please try again.",
                                    icon: "error",
                                    buttonsStyling: false,
                                    confirmButtonText: "Ok, got it!",
                                    customClass: {
                                        confirmButton: "btn btn-primary"
                                    }
                                });
                                submitBtn.removeAttribute("data-kt-indicator");
                                submitBtn.disabled = false;
                            }
                        });
                    } else {
                        Swal.fire({
                            html: "Please fix the errors and try again.<br/><br/>There may be errors in the <strong>General</strong> or <strong>Advanced</strong> tabs.",
                            icon: "error",
                            buttonsStyling: false,
                            confirmButtonText: "Ok, got it!",
                            customClass: { confirmButton: "btn btn-primary" }
                        });
                    }
                });
            });

            initRepeater();
            initSelect2();
        }
    };
}();

KTUtil.onDOMContentLoaded(function () {
    KTAppEcommerceSaveProduct.init();
});
