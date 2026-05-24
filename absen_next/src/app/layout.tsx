import type { Metadata, Viewport } from "next";
import { Cairo } from "next/font/google";
import "./globals.css";
import PwaProvider from "@/components/PwaProvider";

const cairo = Cairo({
  weight: ["400", "700"],
  subsets: ["arabic", "latin"],
  variable: "--font-cairo",
  display: "swap",
});

export const metadata: Metadata = {
  title: "Absensi PP. Matholi'ul Anwar",
  description: "Sistem Absensi Online PP. Matholi'ul Anwar",
  manifest: "/manifest.json",
  icons: {
    icon: [
      { url: '/favicon.ico' },
      { url: '/logo.png', type: 'image/png' }
    ],
    apple: [
      { url: '/logo.png', type: 'image/png' }
    ]
  },
  appleWebApp: {
    capable: true,
    statusBarStyle: "default",
    title: "PPMA Absen",
  },
  formatDetection: {
    telephone: false,
  },
};

export const viewport: Viewport = {
  themeColor: "#166534",
  width: "device-width",
  initialScale: 1,
  maximumScale: 1,
  userScalable: false,
};

export default function RootLayout({
  children,
}: Readonly<{
  children: React.ReactNode;
}>) {
  return (
    <html
      lang="id"
      suppressHydrationWarning
      className={`${cairo.variable} h-full antialiased`}
    >
      <body className="min-h-full flex flex-col">
        <PwaProvider>{children}</PwaProvider>
      </body>
    </html>
  );
}
