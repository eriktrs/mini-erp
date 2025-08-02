# Use the official Node.js 20 Alpine base image (lightweight and production-ready)
FROM node:20-alpine

# Set the working directory inside the container to /app
WORKDIR /app

# Copy only package.json and package-lock.json first
# This allows Docker to cache dependencies layer, improving build performance
COPY frontend/package*.json ./

# Install frontend dependencies listed in package.json
RUN npm install

# Copy the rest of the frontend source code into the container
COPY frontend/ .

# Expose port 5173 so the Vite development server is accessible
EXPOSE 5173

# Run Vite development server with --host to allow external access (Docker host machine)
CMD ["npm", "run", "dev", "--", "--host"]
