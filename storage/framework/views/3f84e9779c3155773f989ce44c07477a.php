<nav id="sidebarMenu" class="col-md-3 col-lg-2 d-md-block bg-body-tertiary sidebar collapse text-capitalize">
      <div class="position-sticky pt-3 sidebar-sticky">
        <ul class="nav flex-column">
          <li class="nav-item">
            <a class="nav-link" aria-current="page" href="<?php echo e(url('admin/dashboard')); ?>">
            <i class="fa-solid fa-gauge" style="padding-right: 5px;"></i>
              Dashboard
            </a>
          </li>
          <?php if(isset($routes)): ?>
          <?php if(is_array($routes)): ?>
          <?php 
          $prefix = 'vk_';
          $filteredTables = array_filter($routes, function ($table) use ($prefix) {
              return strpos($table, $prefix) === 0;
          });
          ?>
          <?php $__currentLoopData = $filteredTables; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $route): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
          <?php $tableName = str_replace("vk_", "", $route); // Eliminamos el prefijo "vk_" ?>
          <li class="nav-item">
            <a class="nav-link" href="<?php echo e(url('admin/grid/'.$route)); ?>">
              <?php  $getIcons= (\Config::get('appweb.admin.icons.'.$key) !== null )? \Config::get('appweb.admin.icons.'.$key): '';
              //dd($getIcons);
              ?>
                <i class="fa-solid fa-<?php echo e($getIcons); ?>" style="padding-right: 5px;"></i> <?php echo e($tableName); ?></a>
          </li>
          <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
          <?php endif; ?>
          <?php endif; ?>
        </ul>
      </div>
    </nav><?php /**PATH /var/www/html/knewsweb.org/resources/views/admin/components/sidebar.blade.php ENDPATH**/ ?>