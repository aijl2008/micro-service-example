FROM node:12.5.0-alpine
WORKDIR /usr/src/app
COPY . .
RUN yarn --only=prod --registry=https://registry.npm.taobao.org
RUN npm install
EXPOSE 3000
CMD [ "node", "app.js" ]