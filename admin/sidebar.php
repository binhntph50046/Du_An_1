<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>

</head> <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
<body>
<div class="app-menu navbar-menu">
    <div class="navbar-brand-box">
        <a href="index.html" class="logo logo-dark">
            <span class="logo-sm"><img src="assets/images/logo-sm.png" alt="" height="22"></span>
            <span class="logo-lg"><img src="assets/images/logo-dark.png" alt="" height="17"></span>
        </a>
    </div>
    <div id="scrollbar">
        <div class="container-fluid">
            <ul class="navbar-nav" id="navbar-nav">
                <li class="menu-title"><span>Dashboard</span></li>
                <li class="nav-item">
                    <a class="nav-link menu-link" href="#sidebarProducts">
                        <i class="ri-shopping-cart-line"></i> <span>Products</span>
                    </a>
                    <div class="collapse menu-dropdown" id="sidebarProducts">
                        <ul class="nav nav-sm flex-column">
                            <li class="nav-item"><a href="#" class="nav-link" data-bs-toggle="modal"
                                    data-bs-target="#addProductModal">Add Product</a></li>
                            <li class="nav-item"><a href="products.html" class="nav-link">Manage Products</a></li>
                            <li class="nav-item"><a href="#" class="nav-link" data-bs-toggle="modal"
                                    data-bs-target="#editProductModal">Edit Product</a></li>
                            <li class="nav-item"><a href="#" class="nav-link" data-bs-toggle="modal"
                                    data-bs-target="#deleteProductModal">Delete Product</a></li>
                        </ul>
                    </div>
                </li>
                <li class="nav-item">
                    <a class="nav-link menu-link" href="#sidebarCategories">
                        <i class="ri-list-check-2"></i> <span>Categories</span>
                    </a>
                    <div class="collapse menu-dropdown" id="sidebarCategories">
                        <ul class="nav nav-sm flex-column">
                            <li class="nav-item"><a href="#" class="nav-link" data-bs-toggle="modal"
                                    data-bs-target="#addCategoryModal">Add Category</a></li>
                            <li class="nav-item"><a href="categories.html" class="nav-link">Manage Categories</a></li>
                            <li class="nav-item"><a href="#" class="nav-link" data-bs-toggle="modal"
                                    data-bs-target="#editCategoryModal">Edit Category</a></li>
                            <li class="nav-item"><a href="#" class="nav-link" data-bs-toggle="modal"
                                    data-bs-target="#deleteCategoryModal">Delete Category</a></li>
                        </ul>
                    </div>
                </li>
                <li class="nav-item">
                    <a class="nav-link menu-link" href="#sidebarOrders">
                        <i class="ri-file-list-3-line"></i> <span>Orders</span>
                    </a>
                    <div class="collapse menu-dropdown" id="sidebarOrders">
                        <ul class="nav nav-sm flex-column">
                            <li class="nav-item"><a href="orders.html" class="nav-link">Manage Orders</a></li>
                        </ul>
                    </div>
                </li>
                <li class="nav-item">
                    <a class="nav-link menu-link" href="#sidebarUsers">
                        <i class="ri-user-line"></i> <span>Users</span>
                    </a>
                    <div class="collapse menu-dropdown" id="sidebarUsers">
                        <ul class="nav nav-sm flex-column">
                            <li class="nav-item"><a href="#" class="nav-link" data-bs-toggle="modal"
                                    data-bs-target="#addUserModal">Add User</a></li>
                            <li class="nav-item"><a href="users.html" class="nav-link">Manage Users</a></li>
                        </ul>
                    </div>
                </li>
                <li class="nav-item">
                    <a class="nav-link menu-link" href="#sidebarComments">
                        <i class="ri-chat-3-line"></i> <span>Comments</span>
                    </a>
                    <div class="collapse menu-dropdown" id="sidebarComments">
                        <ul class="nav nav-sm flex-column">
                            <li class="nav-item"><a href="#" class="nav-link" data-bs-toggle="modal"
                                    data-bs-target="#addCommentModal">Add Comment</a></li>
                            <li class="nav-item"><a href="comments.html" class="nav-link">Manage Comments</a></li>
                        </ul>
                    </div>
                </li>
            </ul>
        </div>
    </div>
</div>
</body>
</html>