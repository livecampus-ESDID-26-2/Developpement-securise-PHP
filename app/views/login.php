<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion - Système de Caisse</title>
    <link rel="stylesheet" href="/views/style.css">
</head>
<body class="login-page">
    <div class="login-container">
        <div class="login-card">
            <div class="login-header">
                <h1>💰 Système de Caisse</h1>
                <p>Veuillez vous connecter pour continuer</p>
            </div>

            <?php if (isset($erreur)): ?>
            <div class="alert alert-error">
                ⚠️ <?php echo htmlspecialchars($erreur); ?>
            </div>
            <?php endif; ?>

            <form method="POST" action="/login" class="login-form">
                <div class="form-group">
                    <label for="email">📧 Email</label>
                    <input 
                        type="email" 
                        id="email" 
                        name="email" 
                        placeholder="user@cash.com"
                        required
                        autofocus
                    >
                </div>

                <div class="form-group">
                    <label for="password">🔒 Mot de passe</label>
                    <input 
                        type="password" 
                        id="password" 
                        name="password" 
                        placeholder="••••••"
                        required
                    >
                </div>

                <button type="submit" class="btn-login">
                    Se connecter
                </button>
            </form>

            <div class="login-footer">
                <div class="demo-accounts">
                    <p><strong>Comptes de démonstration :</strong></p>
                    <div class="demo-list">
                        <div class="demo-account">
                            <strong>👤 Utilisateur 1</strong><br>
                            user1@cash.com / 12345
                        </div>
                        <div class="demo-account">
                            <strong>👤 Utilisateur 2</strong><br>
                            user2@cash.com / 12345
                        </div>
                        <div class="demo-account admin">
                            <strong>👨‍💼 Administrateur</strong><br>
                            admin@cash.com / 123456
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>

