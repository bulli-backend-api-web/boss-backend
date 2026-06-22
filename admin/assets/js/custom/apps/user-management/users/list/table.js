"use strict";
var KTUsersList = function() {
    var e, t, n, r, o = document.getElementById("kt_table_users"),
        // single delete + clear udid binding
        c = () => {
            // 🔹 delete single user
            o.querySelectorAll('[data-kt-users-table-filter="delete_row"]').forEach(btn => {
                btn.addEventListener("click", function(e) {
                    e.preventDefault();
                    const row = e.target.closest("tr");
                    const userId = btn.dataset.id;
                    const userName = row.querySelectorAll("td")[1].querySelectorAll("a")[1].innerText;
                    Swal.fire({
                        text: "Are you sure you want to delete " + userName + "?",
                        icon: "warning",
                        showCancelButton: true,
                        buttonsStyling: false,
                        confirmButtonText: "Yes, delete!",
                        cancelButtonText: "No, cancel",
                        customClass: {
                            confirmButton: "btn fw-bold btn-danger",
                            cancelButton: "btn fw-bold btn-active-light-primary"
                        }
                    }).then(function(result) {
                        if (result.value) {
                            $.ajax({
                                url: "https://vastranand.com/admin/update-user-details",
                                type: "POST",
                                data: {
                                    'customer_id': userId,
                                    'action': 'delete_user'
                                },
                                dataType: "json",
                                success: function(res) {
                                    if (res.status === "success") {
                                        Swal.fire({
                                            text: "You have deleted " + userName + "!",
                                            icon: "success",
                                            buttonsStyling: false,
                                            confirmButtonText: "Ok, got it!",
                                            customClass: {
                                                confirmButton: "btn fw-bold btn-primary"
                                            }
                                        }).then(function() {
                                            $('#kt_table_users').DataTable().row($(row)).remove().draw();
                                            a(); // refresh toolbar selection counts
                                            window.location.reload();
                                        });
                                    } else {
                                        Swal.fire({
                                            text: res.message || "Could not delete " + userName,
                                            icon: "error",
                                            buttonsStyling: false,
                                            confirmButtonText: "Ok, got it!",
                                            customClass: {
                                                confirmButton: "btn fw-bold btn-primary"
                                            }
                                        });
                                    }
                                },
                                error: function() {
                                    Swal.fire({
                                        text: "Server error! " + userName + " was not deleted.",
                                        icon: "error",
                                        buttonsStyling: false,
                                        confirmButtonText: "Ok, got it!",
                                        customClass: {
                                            confirmButton: "btn fw-bold btn-primary"
                                        }
                                    });
                                }
                            });
                        } else if (result.dismiss === "cancel") {
                            Swal.fire({
                                text: userName + " was not deleted.",
                                icon: "error",
                                buttonsStyling: false,
                                confirmButtonText: "Ok, got it!",
                                customClass: {
                                    confirmButton: "btn fw-bold btn-primary"
                                }
                            });
                        }
                    });
                });
            });

            // 🔹 clear UDID
            o.querySelectorAll('[data-kt-users-table-filter="clear-udid"]').forEach(btn => {
                btn.addEventListener("click", function(e) {
                    e.preventDefault();
                    const row = e.target.closest("tr");
                    const userId = btn.dataset.id;
                    const userName = row.querySelectorAll("td")[1].querySelectorAll("a")[1].innerText;

                    Swal.fire({
                        text: "Are you sure you want to clear UDID for " + userName + "?",
                        icon: "warning",
                        showCancelButton: true,
                        buttonsStyling: false,
                        confirmButtonText: "Yes, clear!",
                        cancelButtonText: "No, cancel",
                        customClass: {
                            confirmButton: "btn fw-bold btn-danger",
                            cancelButton: "btn fw-bold btn-active-light-primary"
                        }
                    }).then(function(result) {
                        if (result.value) {
                            $.ajax({
                                url: "https://vastranand.com/admin/update-user-details",
                                type: "POST",
                                data: {
                                    'customer_id': userId,
                                    'action': 'clear_udid'
                                },
                                dataType: "json",
                                success: function(res) {
                                    if (res.status === "success") {
                                        Swal.fire({
                                            text: "UDID cleared for " + userName + "!",
                                            icon: "success",
                                            buttonsStyling: false,
                                            confirmButtonText: "Ok, got it!",
                                            customClass: {
                                                confirmButton: "btn fw-bold btn-primary"
                                            }
                                        }).then(function() {
                                            // you can update row here if needed instead of reload
                                            window.location.reload();
                                        });
                                    } else {
                                        Swal.fire({
                                            text: res.message || "Could not clear UDID for " + userName,
                                            icon: "error",
                                            buttonsStyling: false,
                                            confirmButtonText: "Ok, got it!",
                                            customClass: {
                                                confirmButton: "btn fw-bold btn-primary"
                                            }
                                        });
                                    }
                                },
                                error: function() {
                                    Swal.fire({
                                        text: "Server error! UDID for " + userName + " was not cleared.",
                                        icon: "error",
                                        buttonsStyling: false,
                                        confirmButtonText: "Ok, got it!",
                                        customClass: {
                                            confirmButton: "btn fw-bold btn-primary"
                                        }
                                    });
                                }
                            });
                        } else if (result.dismiss === "cancel") {
                            Swal.fire({
                                text: userName + " UDID was not cleared.",
                                icon: "error",
                                buttonsStyling: false,
                                confirmButtonText: "Ok, got it!",
                                customClass: {
                                    confirmButton: "btn fw-bold btn-primary"
                                }
                            });
                        }
                    });
                });
            });
        },

        // bulk delete
        l = () => {
            const checkboxes = o.querySelectorAll('tbody [type="checkbox"]');
            t = document.querySelector('[data-kt-user-table-toolbar="base"]'),
            n = document.querySelector('[data-kt-user-table-toolbar="selected"]'),
            r = document.querySelector('[data-kt-user-table-select="selected_count"]');

            const deleteSelectedBtn = document.querySelector('[data-kt-user-table-select="delete_selected"]');

            checkboxes.forEach(chk => {
                chk.addEventListener("click", function() {
                    setTimeout(function() {
                        a();
                    }, 50);
                });
            });

            deleteSelectedBtn.addEventListener("click", function() {
                Swal.fire({
                    text: "Are you sure you want to delete selected customers?",
                    icon: "warning",
                    showCancelButton: true,
                    buttonsStyling: false,
                    confirmButtonText: "Yes, delete!",
                    cancelButtonText: "No, cancel",
                    customClass: {
                        confirmButton: "btn fw-bold btn-danger",
                        cancelButton: "btn fw-bold btn-active-light-primary"
                    } 
                }).then(function(result) {
                    if (result.value) {
                        let ids = [];
                        checkboxes.forEach(chk => {
                            if (chk.checked) {
                                let id = chk.closest("tr").querySelector('[data-kt-users-table-filter="delete_row"]').dataset.id;
                                ids.push(id);
                            }
                        });

                        if (ids.length === 0) {
                            Swal.fire("No users selected!", "", "error");
                            return;
                        }

                        $.ajax({
                            url: "https://vastranand.com/admin/update-user-details",
                            type: "POST",
                            data: { 'ids': ids, 'action':'multiple_delete_user' },
                            dataType: "json",
                            success: function(res) {
                                if (res.status === "success") {
                                    Swal.fire({
                                        text: "You have deleted all selected customers!",
                                        icon: "success",
                                        buttonsStyling: false,
                                        confirmButtonText: "Ok, got it!",
                                        customClass: {
                                            confirmButton: "btn fw-bold btn-primary"
                                        }
                                    }).then(function() {
                                        checkboxes.forEach(chk => {
                                            if (chk.checked) {
                                                e.row($(chk.closest("tbody tr"))).remove().draw();
                                            }
                                        });
                                        o.querySelectorAll('[type="checkbox"]')[0].checked = false;
                                        a(); 
                                        l(); 
                                        window.location.reload();
                                    });
                                } else {
                                    Swal.fire({
                                        text: res.message || "Error deleting selected customers.",
                                        icon: "error",
                                        buttonsStyling: false,
                                        confirmButtonText: "Ok, got it!",
                                        customClass: {
                                            confirmButton: "btn fw-bold btn-primary"
                                        }
                                    });
                                }
                            },
                            error: function() {
                                Swal.fire({
                                    text: "Server error while deleting users.",
                                    icon: "error",
                                    buttonsStyling: false,
                                    confirmButtonText: "Ok, got it!",
                                    customClass: {
                                        confirmButton: "btn fw-bold btn-primary"
                                    }
                                });
                            }
                        });
                    } else if (result.dismiss === "cancel") {
                        Swal.fire({
                            text: "Selected customers were not deleted.",
                            icon: "error",
                            buttonsStyling: false,
                            confirmButtonText: "Ok, got it!",
                            customClass: {
                                confirmButton: "btn fw-bold btn-primary"
                            }
                        });
                    }
                });
            });
        };

    const a = () => {
        const e = o.querySelectorAll('tbody [type="checkbox"]');
        let c = !1, l = 0;
        e.forEach(e => {
            e.checked && (c = !0, l++)
        }),
        c ? (r.innerHTML = l, t.classList.add("d-none"), n.classList.remove("d-none")) : (t.classList.remove("d-none"), n.classList.add("d-none"))
    };

    return {
        init: function() {
            o && (o.querySelectorAll("tbody tr").forEach(e => {
                const t = e.querySelectorAll("td"),
                    n = t[3].innerText.toLowerCase();
                let r = 0, o = "minutes";
                n.includes("yesterday") ? (r = 1, o = "days") : 
                n.includes("mins") ? (r = parseInt(n.replace(/\D/g, "")), o = "minutes") : 
                n.includes("hours") ? (r = parseInt(n.replace(/\D/g, "")), o = "hours") : 
                n.includes("days") ? (r = parseInt(n.replace(/\D/g, "")), o = "days") : 
                n.includes("weeks") && (r = parseInt(n.replace(/\D/g, "")), o = "weeks");
                const c = moment().subtract(r, o).format();
                t[3].setAttribute("data-order", c);
                const l = moment(t[5].innerHTML, "DD MMM YYYY, LT").format();
                t[5].setAttribute("data-order", l)
            }), (e = $(o).DataTable({
                info: !1,
                order: [],
                pageLength: 10,
                lengthChange: !1,
                columnDefs: [{
                    orderable: !1,
                    targets: 0
                }, {
                    orderable: !1,
                    targets: 6
                }]
            })).on("draw", function() {
                l(), c(), a()
            }), l(), document.querySelector('[data-kt-user-table-filter="search"]').addEventListener("keyup", function(t) {
                e.search(t.target.value).draw()
            }), document.querySelector('[data-kt-user-table-filter="reset"]').addEventListener("click", function() {
                document.querySelector('[data-kt-user-table-filter="form"]').querySelectorAll("select").forEach(e => {
                    $(e).val("").trigger("change")
                }), e.search("").draw()
            }), c(), (() => {
                const t = document.querySelector('[data-kt-user-table-filter="form"]'),
                    n = t.querySelector('[data-kt-user-table-filter="filter"]'),
                    r = t.querySelectorAll("select");
                n.addEventListener("click", function() {
                    var t = "";
                    r.forEach((e, n) => {
                        e.value && "" !== e.value && (0 !== n && (t += " "), t += e.value)
                    }), e.search(t).draw()
                })
            })())
        }
    }
}();
KTUtil.onDOMContentLoaded(function() {
    KTUsersList.init()
});
