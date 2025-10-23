<footer class="bg-slate-900 text-white mt-auto">
    <div class="container mx-auto px-4 py-12">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
            <!-- Company Info -->
            <div class="space-y-4">
                <div class="flex items-center space-x-2">
                    <div class="w-8 h-8 bg-primary rounded-lg flex items-center justify-center">
                        <span class="text-white font-bold text-lg">P</span>
                    </div>
                    <span class="text-xl font-bold">Phim Việt</span>
                </div>
                <p class="text-slate-300 text-sm leading-relaxed">
                    Nền tảng xem phim Việt Nam hàng đầu với kho tàng phim đa dạng và chất lượng cao.
                </p>
                <div class="flex space-x-4">
                    <a href="#" class="text-slate-400 hover:text-primary transition-colors">
                        <i data-lucide="facebook" class="w-5 h-5"></i>
                    </a>
                    <a href="#" class="text-slate-400 hover:text-primary transition-colors">
                        <i data-lucide="youtube" class="w-5 h-5"></i>
                    </a>
                    <a href="#" class="text-slate-400 hover:text-primary transition-colors">
                        <i data-lucide="instagram" class="w-5 h-5"></i>
                    </a>
                </div>
            </div>

            <!-- Quick Links -->
            <div class="space-y-4">
                <h3 class="text-lg font-semibold">Liên kết nhanh</h3>
                <ul class="space-y-2">
                    <li>
                        <a href="{{ route('home') }}" class="text-slate-300 hover:text-primary transition-colors text-sm">
                            Trang chủ
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('movies.index') }}" class="text-slate-300 hover:text-primary transition-colors text-sm">
                            Phim
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('movies.genres') }}" class="text-slate-300 hover:text-primary transition-colors text-sm">
                            Thể loại
                        </a>
                    </li>
                    <li>
                        <a href="/about" class="text-slate-300 hover:text-primary transition-colors text-sm">
                            Giới thiệu
                        </a>
                    </li>
                </ul>
            </div>

            <!-- Categories -->
            <div class="space-y-4">
                <h3 class="text-lg font-semibold">Thể loại phim</h3>
                <ul class="space-y-2">
                    <li>
                        <a href="{{ route('movies.genres') }}?category=drama" class="text-slate-300 hover:text-primary transition-colors text-sm">
                            Tâm lý
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('movies.genres') }}?category=romance" class="text-slate-300 hover:text-primary transition-colors text-sm">
                            Lãng mạn
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('movies.genres') }}?category=action" class="text-slate-300 hover:text-primary transition-colors text-sm">
                            Hành động
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('movies.genres') }}?category=comedy" class="text-slate-300 hover:text-primary transition-colors text-sm">
                            Hài hước
                        </a>
                    </li>
                </ul>
            </div>

            <!-- Contact Info -->
            <div class="space-y-4">
                <h3 class="text-lg font-semibold">Liên hệ</h3>
                <div class="space-y-3">
                    <div class="flex items-center space-x-3">
                        <i data-lucide="mail" class="w-4 h-4 text-slate-400"></i>
                        <span class="text-slate-300 text-sm">contact@phimstream.vn</span>
                    </div>
                    <div class="flex items-center space-x-3">
                        <i data-lucide="phone" class="w-4 h-4 text-slate-400"></i>
                        <span class="text-slate-300 text-sm">+84 123 456 789</span>
                    </div>
                    <div class="flex items-start space-x-3">
                        <i data-lucide="map-pin" class="w-4 h-4 text-slate-400 mt-0.5"></i>
                        <span class="text-slate-300 text-sm">
                            140 Lê Trọng Tấn, Quận Tân Phú<br />
                            TP. Hồ Chí Minh, Việt Nam
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <div class="border-t border-slate-800 mt-8 pt-8">
            <div class="flex flex-col md:flex-row justify-between items-center space-y-4 md:space-y-0">
                <p class="text-slate-400 text-sm">© 2025 Phim Việt. Tất cả quyền được bảo lưu.</p>
                <div class="flex space-x-6">
                    <a href="#" class="text-slate-400 hover:text-primary transition-colors text-sm">
                        Điều khoản sử dụng
                    </a>
                    <a href="#" class="text-slate-400 hover:text-primary transition-colors text-sm">
                        Chính sách bảo mật
                    </a>
                    <a href="#" class="text-slate-400 hover:text-primary transition-colors text-sm">
                        Hỗ trợ
                    </a>
                </div>
            </div>
        </div>
    </div>
</footer>
