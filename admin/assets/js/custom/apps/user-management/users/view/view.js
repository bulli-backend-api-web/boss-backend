"use strict";
var KTUsersViewMain = {
    init: function() {
        document.getElementById("kt_modal_sign_out_sesions").addEventListener("click", t => {
            t.preventDefault(), Swal.fire({
                text: "Are you sure you would like sign out all sessions?",
                icon: "warning",
                showCancelButton: !0,
                buttonsStyling: !1,
                confirmButtonText: "Yes, sign out!",
                cancelButtonText: "No, return",
                customClass: {
                    confirmButton: "btn btn-primary",
                    cancelButton: "btn btn-active-light"
                }
            }).then(function(t) {
                t.value ? Swal.fire({
                    text: "You have signed out all sessions!.",
                    icon: "success",
                    buttonsStyling: !1,
                    confirmButtonText: "Ok, got it!",
                    customClass: {
                        confirmButton: "btn btn-primary"
                    }
                }) : "cancel" === t.dismiss && Swal.fire({
                    text: "Your sessions are still preserved!.",
                    icon: "error",
                    buttonsStyling: !1,
                    confirmButtonText: "Ok, got it!",
                    customClass: {
                        confirmButton: "btn btn-primary"
                    }
                })
            })
        }), document.querySelectorAll('[data-kt-users-sign-out="single_user"]').forEach(t => {
            t.addEventListener("click", n => {
                n.preventDefault();
                const e = t.closest("tr").querySelectorAll("td")[1].innerText;
                Swal.fire({
                    text: "Are you sure you would like sign out " + e + "?",
                    icon: "warning",
                    showCancelButton: !0,
                    buttonsStyling: !1,
                    confirmButtonText: "Yes, sign out!",
                    cancelButtonText: "No, return",
                    customClass: {
                        confirmButton: "btn btn-primary",
                        cancelButton: "btn btn-active-light"
                    }
                }).then(function(n) {
                    n.value ? Swal.fire({
                        text: "You have signed out " + e + "!.",
                        icon: "success",
                        buttonsStyling: !1,
                        confirmButtonText: "Ok, got it!",
                        customClass: {
                            confirmButton: "btn btn-primary"
                        }
                    }).then(function() {
                        t.closest("tr").remove()
                    }) : "cancel" === n.dismiss && Swal.fire({
                        text: e + "'s session is still preserved!.",
                        icon: "error",
                        buttonsStyling: !1,
                        confirmButtonText: "Ok, got it!",
                        customClass: {
                            confirmButton: "btn btn-primary"
                        }
                    })
                })
            })
        })
    }
};
KTUtil.onDOMContentLoaded(function() {
    KTUsersViewMain.init()
});