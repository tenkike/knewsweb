 

<?php $__env->startSection('admin'); ?>
    <?php if(\Request::segment(2) == 'grid'): ?>
        <div id="content-<?php echo e(\Request::segment(3)); ?>" class="table-responsive p-2">
            <table id="<?php echo e(\Request::segment(3)); ?>" class="table table-secondary table-striped" style="width:100%"></table>
        </div>
    <?php endif; ?>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('css_styles'); ?>
    <link rel="stylesheet" href="https://cdn.datatables.net/2.3.1/css/dataTables.dataTables.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <link rel="stylesheet" href="<?php echo e(asset('admin/css/style.grid.css')); ?>">
<?php $__env->stopSection(); ?>

<?php $__env->startSection('js_script'); ?>
    
    <script src="https://cdn.datatables.net/2.3.1/js/dataTables.js" crossorigin="anonymous"></script>
    <script src="<?php echo e(asset('admin/js/GridDataTable.js')); ?>" crossorigin="anonymous"></script>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('js_grid'); ?>
<script>
// Inicializa la grilla
document.addEventListener('DOMContentLoaded', () => {
    console.log('Inicializando GridDataTable');
    const grid = new GridDataTable();
    grid.initialize();
});
</script>

<?php $__env->stopSection(); ?>
<?php echo $__env->make('admin.index', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /var/www/html/knewsweb.org/resources/views/admin/grid.blade.php ENDPATH**/ ?>