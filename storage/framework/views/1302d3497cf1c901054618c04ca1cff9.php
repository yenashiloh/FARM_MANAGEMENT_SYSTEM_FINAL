<?php if($announcements->isEmpty()): ?>
    <div class="alert alert-info text-center" role="alert">
        No announcement search results found.
    </div>
<?php else: ?>
    <?php $__currentLoopData = $announcements; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $announcement): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <div class="card mb-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <div>
                    <h5 class="mb-1"><?php echo e($announcement->subject); ?></h5>
                    <small class="text-muted">
                        <?php echo e(\Carbon\Carbon::parse($announcement->created_at)->setTimezone('Asia/Manila')->format('F j, Y, g:i a')); ?>

                    </small>
                    <div class="mt-2">
                        To: <?php $__currentLoopData = $announcement->displayEmails; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $email): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <?php echo e($email); ?><?php if(!$loop->last): ?>
                                ,
                            <?php endif; ?>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        <?php if($announcement->moreEmailsCount > 0): ?>
                            and <?php echo e($announcement->moreEmailsCount); ?> more
                        <?php endif; ?>
                    </div>
                </div>
                <div class="d-flex align-items-center">
                    <?php if($announcement->published): ?>
                        <span class="badge badge-success mr-3">Published</span>
                    <?php else: ?>
                        <span class="badge badge-warning mr-3">Unpublished</span>
                    <?php endif; ?>
                    <div class="dropdown">
                        <i class="fas fa-ellipsis-h" id="dropdownMenuButton<?php echo e($announcement->id_announcement); ?>"
                            data-bs-toggle="dropdown" aria-expanded="false"></i>
                        <ul class="dropdown-menu"
                            aria-labelledby="dropdownMenuButton<?php echo e($announcement->id_announcement); ?>">
                            <li><a class="dropdown-item"
                                    href="<?php echo e(route('admin.announcement.edit-announcement', $announcement->id_announcement)); ?>">Edit</a>
                            </li>
                            <li><button type="button" class="dropdown-item delete-btn"
                                    data-id="<?php echo e($announcement->id_announcement); ?>">Delete</button></li>
                            <?php if($announcement->published): ?>
                                <li><a class="dropdown-item"
                                        href="<?php echo e(route('admin.announcement.unpublish-announcement', $announcement->id_announcement)); ?>">Unpublish</a>
                                </li>
                            <?php else: ?>
                                <li><a class="dropdown-item"
                                        href="<?php echo e(route('admin.announcement.publish-announcement', $announcement->id_announcement)); ?>">Publish</a>
                                </li>
                            <?php endif; ?>
                        </ul>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <p class="card-text"><?php echo $announcement->message; ?></p>
            </div>
        </div>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
<?php endif; ?>
<?php /**PATH /home/u687103837/domains/pupt-farm.com/public_html/resources/views/admin/announcement/announcement-list.blade.php ENDPATH**/ ?>