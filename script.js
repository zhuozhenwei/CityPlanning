// 获取所有的菜单按钮和菜单
const menuBtns = document.querySelectorAll('.menu-btn');
const menus = document.querySelectorAll('.menu');
const deleteBtns = document.querySelectorAll('.delete-btn');

// 点击菜单按钮时显示/隐藏对应的菜单
menuBtns.forEach((menuBtn, index) => {
    menuBtn.addEventListener('click', function(event) {
        event.stopPropagation(); // 防止点击菜单本身触发关闭
        menus[index].style.display = menus[index].style.display === 'block' ? 'none' : 'block';
    });
});

// 点击页面其他地方时关闭所有菜单
document.addEventListener('click', function() {
    menus.forEach(menu => {
        menu.style.display = 'none';
    });
});

// 点击删除按钮时触发删除操作
deleteBtns.forEach((deleteBtn, index) => {
    deleteBtn.addEventListener('click', function(event) {
        event.stopPropagation(); // 防止关闭菜单
        const postId = document.querySelectorAll('.reply-item')[index].dataset.postId; // 获取当前回复的ID

        // 这里可以加入删除逻辑，例如发送请求删除回复
        if (confirm('确定删除该回复吗？')) {
            // 删除操作，通常会是一个 AJAX 请求或者表单提交
            alert('删除操作');
            // 在这里您可以执行实际的删除请求，例如：
            // 使用Ajax发送删除请求：
            // fetch('delete_reply.php', { method: 'POST', body: JSON.stringify({ postId: postId }) })
            //     .then(response => response.json())
            //     .then(data => alert(data.message))
            //     .catch(error => console.error('Error:', error));
        }
    });
});
