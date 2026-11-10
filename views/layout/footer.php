</div> 

<!-- Ruta corregida para cargar el JS local -->
<script src="./assets/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/jquery@3.7.1/dist/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/datatables.net@2.1.8/js/dataTables.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.7/dist/chart.umd.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.15.10"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('table.table-interactive').forEach(function (table) {
        if (window.DataTable) {
            new DataTable(table, {
                pageLength: 10,
                language: {
                    search: 'Buscar:',
                    lengthMenu: 'Mostrar _MENU_',
                    info: 'Mostrando _START_ a _END_ de _TOTAL_ registros',
                    infoEmpty: 'Sin registros',
                    zeroRecords: 'No se encontraron resultados',
                    paginate: { previous: 'Anterior', next: 'Siguiente' }
                }
            });
        }
    });

    document.querySelectorAll('form[data-confirm]').forEach(function (form) {
        form.addEventListener('submit', function (event) {
            if (!window.Swal) return;
            event.preventDefault();
            Swal.fire({
                title: '¿Confirmar acción?',
                text: form.dataset.confirm,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc3545',
                cancelButtonText: 'Cancelar',
                confirmButtonText: 'Sí, continuar'
            }).then(function (result) {
                if (result.isConfirmed) form.submit();
            });
        });
    });
});
</script>
</body>
</html>