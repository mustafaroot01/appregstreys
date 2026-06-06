#!/bin/bash

echo "🚀 Starting deployment build process..."

echo "📦 1. Building the Next.js Frontend..."
cd frontend

# Set NEXT_PUBLIC_API_URL to empty string.
# This makes the frontend automatically connect to the current domain it's hosted on!
export NEXT_PUBLIC_API_URL=""

# Build the Next.js app to static HTML
npm install
npm run build

echo "🚚 2. Copying static files to Laravel public directory..."
# Copy all contents from frontend/out to backend/public
cp -R out/* ../backend/public/

cd ..
echo "🎉 Deployment Build Complete!"
echo "--------------------------------------------------------"
echo "✅ All frontend files are now merged into the Backend."
echo "✅ To deploy, simply upload the 'backend' folder to your server (cPanel/Hostinger/etc)."
echo "--------------------------------------------------------"
