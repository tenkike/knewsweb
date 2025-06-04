<header class="navbar navbar-dark sticky-top bg-dark flex-md-nowrap p-0 shadow">
  <a class="navbar-brand col-md-3 col-lg-2 me-0 px-3 fs-6" href="<?php echo e(url('admin/dashboard')); ?>">
  <i class="fa-solid fa-compass fa-lg" style="padding-right: 5px;"></i>
    <?php echo e(config('app.name').' Admin'); ?>

  </a>
  <button class="navbar-toggler position-absolute d-md-none collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#sidebarMenu" aria-controls="sidebarMenu" aria-expanded="false" aria-label="Toggle navigation">
    <span class="navbar-toggler-icon"></span>
  </button>
  <input class="form-control form-control-dark w-100 rounded-0 border-0" type="text" placeholder="Search" aria-label="Search">
  
  <div class="btn-group w-50 text-capitalize">
  <button type="button" class="btn btn-dark dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
    Configuracion
  </button>
  <ul class="dropdown-menu dropdown-menu-center w-100">
      <?php if(isset($routes)): ?>

      
      <?php if(is_array($routes)): ?>
        <?php 
        $prefix = 'vk_';
        $filteredTables = array_filter($routes, function ($table) use ($prefix) {
            return strpos($table, $prefix) !== 0;
        });
      ?>
      <?php $__currentLoopData = $filteredTables; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key=> $route): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
      <?php $tableName = str_replace("_", " ", $route);
           $getIcons= (\Config::get('appweb.admin.icons.'.$key) !== null )? \Config::get('appweb.admin.icons.'.$key): '';
              //dd($getIcons);
      ?>
      <li>
        <a class="dropdown-item" href="<?php echo e(url('admin/grid/'.$route)); ?>">
        <i class="fa-solid fa-<?php echo e($getIcons); ?>" style="padding-right: 5px;"></i>     
        <?php echo e($tableName); ?>

      </a>
      
      </li>
      <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
      <li><hr class="dropdown-divider"></li>
      <?php endif; ?>
      <?php endif; ?>
      <?php if(auth()->guard()->guest()): ?>
      <?php else: ?>
        <li>
            <a title="<?php echo e(Auth::user()->name); ?>" class="dropdown-item text-success" style="color: <?php echo e((Auth::user()->name) ? 'green !important': 'red !important'); ?> " href="#" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
            <i class="fa-solid fa-user" style="padding-right: 5px;"></i>  
            
              <?php echo e(Auth::user()->name); ?>

            </a>
            <a title="Cerrar session" class="dropdown-item logout" href="<?php echo e(route('logout')); ?>"
              onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
              <i class="fa-solid fa-right-from-bracket" style="padding-right: 5px;"></i> 
                <?php echo e(__('Logout')); ?>

            </a>
            <form id="logout-form" action="<?php echo e(route('logout')); ?>" method="POST" class="d-none">
                <?php echo csrf_field(); ?>
            </form>
        </li>
    <?php endif; ?>
  </ul>
</div>
</header>

<?php /**PATH /var/www/html/knewsweb.org/resources/views/admin/components/header.blade.php ENDPATH**/ ?>