# Stage 1: Build Vue Application
FROM node:20-alpine AS build-stage
WORKDIR /app
COPY package*.json ./
RUN npm ci
COPY . .

ARG VITE_API_BASE_URL
ENV VITE_API_BASE_URL=$VITE_API_BASE_URL

ARG VITE_APP_NAME
ENV VITE_APP_NAME=$VITE_APP_NAME

ARG VITE_REVERB_APP_KEY
ENV VITE_REVERB_APP_KEY=$VITE_REVERB_APP_KEY

ARG VITE_REVERB_HOST
ENV VITE_REVERB_HOST=$VITE_REVERB_HOST

RUN npm run build

# Stage 2: Serve Built Files with Nginx
FROM nginx:alpine AS production-stage
COPY --from=build-stage /app/dist /usr/share/nginx/html
EXPOSE 80
CMD ["nginx", "-g", "daemon off;"]