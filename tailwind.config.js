/** @type {import('tailwindcss').Config} */
module.exports = {
  content: [
    // 2. مسارات ملفات القالب الفرعي الحالي (portfolio_theme)
    "./templates/**/*.tpl.php",
    "./*.php"
  ],
  safelist: [
    'text-gray-400',
    'sm\:w-52',
    'sm\:h-52',
    'shadow-2xl',
    'bg-\[\#091b24\]',
    'border-white\/15',
    'sm\:w-auto',
    'sm\:text-5xl',
    'leading-tight',
    'text-center',
    'mb-8',
    'gap-4',
    'px-6',
    'py-2.5',
    'bg-white',
    'text-[#051218]',
    'font-medium', 'hover:bg-gray-100', 'transition', 'shadow-md', 'text-sm', 'sm\:flex-row',
    'px-4', 'pt-2', 'pb-12',
  ],
  theme: {
    extend: {
      // يمكنك تخصيص الألوان، الخطوط، أو المتغيرات هنا
    },
  },
  plugins: [],
}