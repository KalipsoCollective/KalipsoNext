        <nav class="navbar navbar-expand-lg navbar-dark kn-nav">
            <div class="container-fluid">
                <a class="navbar-brand" href="#"><?php echo self::config('app.name'); ?></a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarNav">
                    <ul class="navbar-nav">
                        <li class="nav-item">
                            <a class="nav-link active" aria-current="page" href="#">Home</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="/account">Account</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="/account/login">Login</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="/account/register">Register</a>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>