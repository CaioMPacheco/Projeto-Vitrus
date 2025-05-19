<!-- Footer -->
<footer class="main-footer">
    <div class="footer-content">
        <div class="footer-section about">
            <div class="logo-footer">
                <i class="fas fa-tools"></i>
                <h2>Assistec</h2>
            </div>
            <p>Soluções em assistência técnica e produtos de tecnologia com qualidade e excelência desde 2010.</p>
            <div class="contact">
                <span><i class="fas fa-phone"></i> &nbsp; (11) 9999-8888</span>
                <span><i class="fas fa-envelope"></i> &nbsp; contato@assistec.com.br</span>
            </div>
            <div class="socials">
                <a href="#"><i class="fab fa-facebook"></i></a>
                <a href="#"><i class="fab fa-instagram"></i></a>
                <a href="#"><i class="fab fa-twitter"></i></a>
                <a href="#"><i class="fab fa-linkedin"></i></a>
            </div>
        </div>

        <div class="footer-section links">
            <h2>Links Rápidos</h2>
            <ul>
                <li><a href="dashboard.php">Dashboard</a></li>
                <li><a href="usuarios.php">Usuários</a></li>
                <li><a href="./php/relatórioProdutos.php">Relatórios</a></li>
                <li><a href="./perfil/perfil.php">Perfil</a></li>
                <li><a href="./login/index.html">Login</a></li>
            </ul>
        </div>

        <div class="footer-section newsletter">
            <h2>Newsletter</h2>
            <p>Inscreva-se para receber nossas novidades e promoções.</p>
            <form action="#" method="post">
                <input type="email" name="email" class="text-input newsletter-input" placeholder="Digite seu email...">
                <button type="submit" class="btn-newsletter">Inscrever</button>
            </form>
        </div>
    </div>

    <div class="footer-bottom">
        <p>&copy; <?php echo date('Y'); ?> Assistec. Todos os direitos reservados.</p>
    </div>
</footer>

<script>
    // Script para ajustar links do footer conforme a localização da página atual
    document.addEventListener('DOMContentLoaded', function() {
        // Obtém o caminho da página atual
        const currentPath = window.location.pathname;
        
        // Obtém todos os links do footer
        const footerLinks = document.querySelectorAll('.footer-section.links a');
        
        // Para cada link, ajusta o caminho relativo se necessário
        footerLinks.forEach(link => {
            let href = link.getAttribute('href');
            
            // Se estamos em uma subpasta e o link não começa com / ou http
            if (currentPath.includes('/') && !href.startsWith('/') && !href.startsWith('http')) {
                // Contamos quantos níveis estamos abaixo da raiz
                const pathSegments = currentPath.split('/').filter(Boolean);
                const depth = pathSegments.length - 1; // -1 porque o último é o nome do arquivo
                
                if (depth > 0) {
                    // Adicionamos ../ conforme necessário
                    let prefix = '';
                    for (let i = 0; i < depth; i++) {
                        prefix += '../';
                    }
                    link.setAttribute('href', prefix + href);
                }
            }
        });
    });
</script>