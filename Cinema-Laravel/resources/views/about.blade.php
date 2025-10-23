@extends('layouts.app')

@section('title', 'Về chúng tôi - Phim Việt')
@section('description', 'Tìm hiểu về Phim Việt - nền tảng streaming hàng đầu dành riêng cho điện ảnh Việt Nam')

@section('content')
<div class="min-h-screen bg-background flex flex-col">
    <main class="flex-1">
        <div class="container mx-auto px-4 py-8">
            <!-- Hero Section -->
            <div class="text-center mb-16">
                <h1 class="text-5xl font-bold text-foreground mb-6">
                    Về <span class="text-primary">Phim Việt</span>
                </h1>
                <p class="text-xl text-muted-foreground max-w-3xl mx-auto leading-relaxed">
                    Chúng tôi là nền tảng streaming hàng đầu dành riêng cho điện ảnh Việt Nam, mang đến cho bạn những tác phẩm
                    điện ảnh đặc sắc từ quê hương.
                </p>
            </div>

            <!-- Team Section -->
            <div class="mb-16">
                <h2 class="text-3xl font-bold text-center mb-8">Đội ngũ của chúng tôi</h2>
                <div class="grid md:grid-cols-2 lg:grid-cols-4 gap-8">
                    <div class="text-center hover:shadow-lg transition-shadow bg-card border rounded-lg">
                        <div class="p-6">
                            <img src="https://scontent.fsgn5-15.fna.fbcdn.net/v/t39.30808-6/416352891_3958475811046188_3813556086610444868_n.jpg?_nc_cat=111&ccb=1-7&_nc_sid=6ee11a&_nc_eui2=AeGDw3BQQpaC2_CHxxtK_zNHEkxC3BbLNAcSTELcFss0B-V2pK0VjSgTZWJcCI-hzQjrqy7gRv_aUYymTGjJPpTz&_nc_ohc=i2oFA-OFHv8Q7kNvwHgGaJa&_nc_oc=AdlnxsWBZupGu7qw5IDg8emnngg7EJQ13UhOt11kXznAP489ersuIe2HfgAY6svXpAU&_nc_zt=23&_nc_ht=scontent.fsgn5-15.fna&_nc_gid=7shFR616z-eFr5n8bVAMQQ&oh=00_AfWJ0cwz54R4d0sIohjEaDS7LBfTJOOyC-jKR6tUaGpDUg&oe=68AB3F5A" 
                                 alt="Hứa Minh Hoàng" 
                                 class="w-20 h-20 rounded-full mx-auto mb-4 object-cover"
                                 onerror="this.src='/images/placeholder-avatar.svg'">
                            <h3 class="text-xl font-semibold mb-2 text-foreground">Hứa Minh Hoàng</h3>
                            <span class="inline-flex items-center rounded-full border px-2.5 py-0.5 text-xs font-semibold transition-colors focus:outline-none focus:ring-2 focus:ring-ring focus:ring-offset-2 border-transparent bg-secondary text-secondary-foreground mb-3">
                                Thành viên nhóm
                            </span>
                            <p class="text-muted-foreground text-sm leading-relaxed">Sinh viên K13 khoa công nghệ thông tin</p>
                        </div>
                    </div>

                    <div class="text-center hover:shadow-lg transition-shadow bg-card border rounded-lg">
                        <div class="p-6">
                            <img src="https://scontent.fsgn5-10.fna.fbcdn.net/v/t39.30808-6/406787321_3553725478217104_3280298165312313883_n.jpg?_nc_cat=106&ccb=1-7&_nc_sid=6ee11a&_nc_eui2=AeFwZsd_n1Yj7SfCivL5HSpAVr-86cyfcQpWv7zpzJ9xCkNstcV4OCcAxn-JKWBXx9FYqUsUjUX9ClrlC_WVuhDr&_nc_ohc=cvOPo6Vek4kQ7kNvwEs92gY&_nc_oc=AdmFDIw-8iewFh_fPJjxvpXGUfnL2dQKFYYKKajBlJUptMGJnLduLefOeC5w9YrVqFc&_nc_zt=23&_nc_ht=scontent.fsgn5-10.fna&_nc_gid=RaCk3kIxesiRuGs9gDh8Ww&oh=00_AfXsMYFJoIygHd-dDxtJ7lv7_9FUKIxwYO953_N3EQiOLQ&oe=68AB4A52" 
                                 alt="Trịnh Đặng Thành Nam" 
                                 class="w-20 h-20 rounded-full mx-auto mb-4 object-cover"
                                 onerror="this.src='/images/placeholder-avatar.svg'">
                            <h3 class="text-xl font-semibold mb-2 text-foreground">Trịnh Đặng Thành Nam</h3>
                            <span class="inline-flex items-center rounded-full border px-2.5 py-0.5 text-xs font-semibold transition-colors focus:outline-none focus:ring-2 focus:ring-ring focus:ring-offset-2 border-transparent bg-secondary text-secondary-foreground mb-3">
                                Thành viên nhóm
                            </span>
                            <p class="text-muted-foreground text-sm leading-relaxed">Sinh viên K13 khoa công nghệ thông tin</p>
                        </div>
                    </div>

                    <div class="text-center hover:shadow-lg transition-shadow bg-card border rounded-lg">
                        <div class="p-6">
                            <img src="https://i.imgur.com/QGrJxJg.jpeg" 
                                 alt="Trần Minh Trí" 
                                 class="w-20 h-20 rounded-full mx-auto mb-4 object-cover"
                                 onerror="this.src='/images/placeholder-avatar.svg'">
                            <h3 class="text-xl font-semibold mb-2 text-foreground">Trần Minh Trí</h3>
                            <span class="inline-flex items-center rounded-full border px-2.5 py-0.5 text-xs font-semibold transition-colors focus:outline-none focus:ring-2 focus:ring-ring focus:ring-offset-2 border-transparent bg-secondary text-secondary-foreground mb-3">
                                Thành viên nhóm
                            </span>
                            <p class="text-muted-foreground text-sm leading-relaxed">Sinh viên K13 khoa công nghệ thông tin</p>
                        </div>
                    </div>

                    <div class="text-center hover:shadow-lg transition-shadow bg-card border rounded-lg">
                        <div class="p-6">
                            <img src="/images/placeholder-avatar.svg" 
                                 alt="Lê Huỳnh Tấn Phước" 
                                 class="w-20 h-20 rounded-full mx-auto mb-4 object-cover"
                                 onerror="this.src='/images/placeholder-avatar.svg'">
                            <h3 class="text-xl font-semibold mb-2 text-foreground">Lê Huỳnh Tấn Phước</h3>
                            <span class="inline-flex items-center rounded-full border px-2.5 py-0.5 text-xs font-semibold transition-colors focus:outline-none focus:ring-2 focus:ring-ring focus:ring-offset-2 border-transparent bg-secondary text-secondary-foreground mb-3">
                                Thành viên nhóm
                            </span>
                            <p class="text-muted-foreground text-sm leading-relaxed">Sinh viên K13 khoa công nghệ thông tin</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Features Section -->
            <div class="mb-16">
                <h2 class="text-3xl font-bold text-center mb-8">Tại sao chọn Phim Việt?</h2>
                <div class="grid md:grid-cols-2 gap-8">
                    <div class="hover:shadow-lg transition-shadow bg-card border rounded-lg">
                        <div class="p-6">
                            <h3 class="text-xl font-semibold mb-3 text-foreground">Bộ sưu tập phong phú</h3>
                            <p class="text-muted-foreground leading-relaxed">Hàng trăm bộ phim Việt Nam từ cổ điển đến hiện đại, từ nghệ thuật đến thương mại.</p>
                        </div>
                    </div>

                    <div class="hover:shadow-lg transition-shadow bg-card border rounded-lg">
                        <div class="p-6">
                            <h3 class="text-xl font-semibold mb-3 text-foreground">Chất lượng cao</h3>
                            <p class="text-muted-foreground leading-relaxed">Tất cả phim đều được digitize với chất lượng HD, âm thanh rõ ràng.</p>
                        </div>
                    </div>

                    <div class="hover:shadow-lg transition-shadow bg-card border rounded-lg">
                        <div class="p-6">
                            <h3 class="text-xl font-semibold mb-3 text-foreground">Phụ đề đầy đủ</h3>
                            <p class="text-muted-foreground leading-relaxed">Phụ đề tiếng Việt và tiếng Anh cho tất cả các bộ phim.</p>
                        </div>
                    </div>

                    <div class="hover:shadow-lg transition-shadow bg-card border rounded-lg">
                        <div class="p-6">
                            <h3 class="text-xl font-semibold mb-3 text-foreground">Cộng đồng yêu phim</h3>
                            <p class="text-muted-foreground leading-relaxed">Kết nối với những người yêu điện ảnh Việt Nam trên khắp thế giới.</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Vision Section -->
            <div class="text-center">
                <div class="bg-gradient-to-r from-secondary/10 to-primary/10 border-secondary/20 border rounded-lg">
                    <div class="p-8">
                        <h2 class="text-3xl font-bold mb-6">Tầm nhìn tương lai</h2>
                        <p class="text-lg text-muted-foreground max-w-4xl mx-auto leading-relaxed mb-6">
                            Chúng tôi hướng tới việc trở thành nền tảng streaming số 1 cho điện ảnh Việt Nam, không chỉ tại Việt
                            Nam mà còn trên toàn thế giới. Chúng tôi sẽ tiếp tục mở rộng bộ sưu tập, cải thiện trải nghiệm người
                            dùng và xây dựng cộng đồng yêu phim mạnh mẽ.
                        </p>
                        <span class="inline-flex items-center rounded-full border px-4 py-2 text-lg font-semibold transition-colors focus:outline-none focus:ring-2 focus:ring-ring focus:ring-offset-2 border-input bg-background hover:bg-accent hover:text-accent-foreground">
                            Điện ảnh Việt - Tự hào dân tộc
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </main>
</div>
@endsection

@push('styles')
<style>
    .bg-card {
        background-color: hsl(var(--card));
    }
    
    .text-card-foreground {
        color: hsl(var(--card-foreground));
    }
    
    .bg-secondary {
        background-color: hsl(var(--secondary));
    }
    
    .text-secondary-foreground {
        color: hsl(var(--secondary-foreground));
    }
    
    .border-secondary {
        border-color: hsl(var(--secondary));
    }
</style>
@endpush
