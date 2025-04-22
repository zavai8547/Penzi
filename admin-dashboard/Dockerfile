FROM node:lts as build-stage
WORKDIR /app
COPY . .
RUN yarn
RUN yarn build
#RUN export DISABLE_ESLINT_PLUGIN=true && npm i && npm run build

FROM nginx:1.15
COPY --from=build-stage /app/build/ /usr/share/nginx/html
COPY --from=build-stage /app/nginx.conf /etc/nginx/conf.d/default.conf
