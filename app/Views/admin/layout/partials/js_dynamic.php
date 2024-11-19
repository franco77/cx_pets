function refreshTable() {
datatable.ajax.reload();
}

function ajaxRequest(url, data, successMessage, errorMessage) {
$.ajax({
type: "POST",
url: url,
data: data,
cache: false,
processData: false,
contentType: false,
})
.done(function () {
refreshTable();
$(".modal-form").modal("hide");
toastr.success(successMessage);
})
.fail(function (res) {
$(".form-text").remove();
$(".is-invalid").removeClass("is-invalid");
const errors = jQuery.parseJSON(res.responseText);
$.each(errors.messages, function (selector, value) {
$('[for="' + selector + '"]').after(
'<small class="form-text text-danger">' + value + "</small>"
);
$('[name="' + selector + '"]').addClass("is-invalid");
});
toastr.error(errorMessage);
});
}

function deleteItems(ids) {
Swal.fire({
title: "¿Estás seguro?",
text: "Esta acción no se puede deshacer.",
icon: "warning",
showCancelButton: true,
confirmButtonColor: "#3085d6",
cancelButtonColor: "#d33",
confirmButtonText: "Sí, ¡eliminar!",
cancelButtonText: "Cancelar",
}).then((result) => {
if (result.isConfirmed) {
const requests = ids.map((id) =>
$.ajax({
url: host + "delete/" + id,
type: "POST",
dataType: "json",
})
);

$.when
.apply($, requests)
.done(() => {
refreshTable();
Swal.fire(
"¡Eliminado!",
"Los registros han sido eliminados.",
"success"
);
})
.fail((jqXHR, textStatus, errorThrown) => {
console.error(
"Error al eliminar los registros:",
textStatus,
errorThrown
);
Swal.fire(
"Error",
"Hubo un problema al eliminar los registros. Inténtalo de nuevo.",
"error"
);
});
}
});
}

datatable.on("draw", function () {
$(".form-action").on("click", function () {
const button = $(this);
const modalForm = $(".modal-form");
const itemId = button.attr("item-id");
const purpose = button.attr("purpose");

let title, url, submitUrl;
if (purpose === "add") {
title = "Add Data";
url = host + "new";
submitUrl = host + "create";
} else if (purpose === "edit") {
title = "Edit Data";
url = host + "edit/" + itemId;
submitUrl = host + "update/" + itemId;
} else {
title = "Detail Data";
url = host + "show/" + itemId;
}

$.ajax({
type: "GET",
url: url,
})
.done(function (response) {
modalForm.find(".modal-title").text(title);
modalForm.find(".modal-body").html(response);
modalForm.modal("show");
initializePlugins();

$("#form input:text, #form textarea").first().focus();
$("#form").on("submit", function (e) {
e.preventDefault();
const formData = new FormData(this);
ajaxRequest(
submitUrl,
formData,
"Registro guardado con éxito",
"Error al guardar el registro"
);
});
})
.fail(function () {
alert("Data not found");
});
});
});

$(".refresh").on("click", refreshTable);

$(".check-items").on("click", function () {
$("input:checkbox").not(this).prop("checked", this.checked);
});

$(".bulk-delete").on("click", function () {
const ids = $(".bulk-item:checked")
.map(function () {
return $(this).val();
})
.get();

if (ids.length) {
deleteItems(ids);
} else {
Swal.fire({
icon: "error",
title: "Nada Seleccionado",
text: "Por favor selecciona algún registro para borrar!",
});
}
});

function initializePlugins() {
$(".select2").select2({
dropdownParent: $("#form"),
});
$('#summernote').summernote({
height: 250,
toolbar: [
['style', ['style']],
['font', ['bold', 'underline', 'clear']],
['color', ['color']],
['para', ['ul', 'ol', 'paragraph']],
['table', ['table']],
]
});
}