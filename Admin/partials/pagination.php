<?php
if (!isset($page) || !isset($total_pages)) {
    echo '<!-- Pagination parameters not set -->';
    return;
}
?>

<div class="flex justify-center mt-8">
    <nav class="inline-flex rounded-md shadow-sm" aria-label="Pagination">
        <a href="?page=1" class="px-3 py-2 border border-gray-300 bg-white text-gray-500 hover:bg-indigo-50 rounded-l-md <?php if($page==1) echo 'pointer-events-none opacity-50'; ?>">First</a>
        <a href="?page=<?php echo max(1, $page-1); ?>" class="px-3 py-2 border-t border-b border-gray-300 bg-white text-gray-500 hover:bg-indigo-50 <?php if($page==1) echo 'pointer-events-none opacity-50'; ?>">Prev</a>
        <?php for($i=1; $i<=$total_pages; $i++): ?>
            <a href="?page=<?php echo $i; ?>" class="px-3 py-2 border-t border-b border-gray-300 <?php echo $i==$page ? 'bg-indigo-600 text-white' : 'bg-white text-gray-700 hover:bg-indigo-50'; ?>"><?php echo $i; ?></a>
        <?php endfor; ?>
        <a href="?page=<?php echo min($total_pages, $page+1); ?>" class="px-3 py-2 border-t border-b border-gray-300 bg-white text-gray-500 hover:bg-indigo-50 <?php if($page==$total_pages) echo 'pointer-events-none opacity-50'; ?>">Next</a>
        <a href="?page=<?php echo $total_pages; ?>" class="px-3 py-2 border border-gray-300 bg-white text-gray-500 hover:bg-indigo-50 rounded-r-md <?php if($page==$total_pages) echo 'pointer-events-none opacity-50'; ?>">Last</a>
    </nav>
</div>
