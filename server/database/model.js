const { dbQuery } = require("./index");
const cache = require("./../lib/cache");

const myCache = cache.myCache;

// autoreplies table
const isExistsEqualCommand = async (command, number) => {
  // find in autoreplies where has device.body = numberWa

  if (myCache.has(command + number)) {
    return myCache.get(command + number);
  }
  let checkDevice = await dbQuery(
    `SELECT * FROM devices WHERE body = '${number}' LIMIT 1`
  );
  if (checkDevice.length === 0) return [];
  let device_id = checkDevice[0].id;
  
  let data = await dbQuery(
    `SELECT * FROM autoreplies WHERE keyword = "${command}" AND type_keyword = 'Equal' AND device_id = ${device_id} AND status = 'Active' LIMIT 1`
  );

  if (data.length === 0) return [];
  myCache.set(command + number, data);
  return data;
};

const isExistsContainCommand = async (command, number) => {
  // find in autoreplies where has device.body = numberWa
  if (myCache.has("contain" + command + number)) {
    return myCache.get("contain" + command + number);
  }
  let checkDevice = await dbQuery(
    `SELECT * FROM devices WHERE body = '${number}' LIMIT 1`
  );
  if (checkDevice.length === 0) return [];
  let device_id = checkDevice[0].id;
  let data = await dbQuery(
    `SELECT * FROM autoreplies WHERE LOCATE(keyword, "${command}") > 0 AND type_keyword = 'Contain' AND device_id = ${device_id} AND status = 'Active' LIMIT 1`
  );
  if (data.length === 0) return [];

  myCache.set("contain" + command + number, data);
  return data;
};

const getUrlWebhook = async (number) => {
  if (myCache.has("webhook" + number)) {
    return myCache.get("webhook" + number);
  }
  let url = null;
  let data = await dbQuery(
    `SELECT webhook FROM devices WHERE body = '${number}' LIMIT 1`
  );
  if (data.length > 0) {
    url = data[0].webhook;
  }
  myCache.set("webhook" + number, url);
  return url;
};
//  end autoreplies table

module.exports = {
  isExistsEqualCommand,
  isExistsContainCommand,
  getUrlWebhook,
};
